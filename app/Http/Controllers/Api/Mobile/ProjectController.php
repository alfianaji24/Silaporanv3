<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectMember;
use App\Models\ProjectTaskMember;
use App\Models\ProjectTaskComment;
use App\Models\ProjectTaskAttachment;
use App\Models\ProjectTaskLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Get NIK of authenticated employee.
     */
    private function getNik($userId)
    {
        return DB::table('users_karyawan')->where('id_user', $userId)->value('nik');
    }

    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $nik = $this->getNik($user->id);

        if (empty($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data Karyawan.'
            ], 404);
        }

        // Get projects where employee is a member
        $projects = Project::whereHas('members', function($q) use ($nik) {
            $q->where('nik', $nik);
        })
        ->with(['category', 'members.karyawan'])
        ->orderBy('end_date', 'asc')
        ->get();

        $data = $projects->map(function ($project) use ($nik) {
            // Count tasks assigned to this employee in this project that are not completed
            $pendingTasksCount = ProjectTask::where('project_id', $project->id)
                ->where('status', '!=', 'completed')
                ->whereHas('members', function($q) use ($nik) {
                    $q->where('nik', $nik);
                })
                ->count();

            $isLeader = $project->members->where('role', 'leader')->where('nik', $nik)->isNotEmpty();
            $leader = $project->members->where('role', 'leader')->first();
            $leaderName = $leader && $leader->karyawan ? ($leader->karyawan->nama_lengkap ?? $leader->karyawan->nama_karyawan) : 'Belum ditentukan';
            $categoryColor = $project->category && $project->category->warna ? $project->category->warna : '#64748b';

            return [
                'id' => $project->id,
                'kode_project' => $project->kode_project,
                'nama_project' => $project->nama_project,
                'deskripsi' => $project->deskripsi ?? '',
                'category' => $project->category ? $project->category->nama_kategori : 'Uncategorized',
                'category_color' => $categoryColor,
                'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : '',
                'end_date' => $project->end_date ? $project->end_date->format('Y-m-d') : '',
                'status' => $project->status ?? 'active',
                'prioritas' => $project->prioritas ?? 'medium',
                'progress' => (int)($project->progress ?? 0),
                'members_count' => $project->members->count(),
                'pending_tasks_count' => $pendingTasksCount,
                'leader_name' => $leaderName,
                'is_leader' => $isLeader
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Display the specified project with tasks and members.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $nik = $this->getNik($user->id);

        if (empty($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data Karyawan.'
            ], 404);
        }

        $project = Project::with(['category', 'members.karyawan'])->find($id);

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project tidak ditemukan.'
            ], 404);
        }

        // Verify membership
        $isMember = DB::table('project_members')->where('project_id', $project->id)->where('nik', $nik)->exists();
        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke project ini.'
            ], 403);
        }

        $isLeader = DB::table('project_members')
            ->where('project_id', $project->id)
            ->where('nik', $nik)
            ->where('role', 'leader')
            ->exists();

        // Get all tasks for this project
        $tasks = ProjectTask::where('project_id', $project->id)
            ->with(['members.karyawan'])
            ->orderBy('urutan', 'asc')
            ->orderBy('due_date', 'asc')
            ->get();

        $formattedTasks = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'parent_id' => $task->parent_id,
                'kode_task' => $task->kode_task,
                'judul' => $task->judul,
                'deskripsi' => $task->deskripsi ?? '',
                'status' => $task->status ?? 'todo',
                'prioritas' => $task->prioritas ?? 'medium',
                'progress' => (int)($task->progress ?? 0),
                'start_date' => $task->start_date ? $task->start_date->format('Y-m-d') : '',
                'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : '',
                'members' => $task->members->map(function ($m) {
                    return [
                        'nik' => $m->nik,
                        'nama' => $m->karyawan ? ($m->karyawan->nama_lengkap ?? $m->karyawan->nama_karyawan) : 'Karyawan',
                        'foto' => $m->karyawan && $m->karyawan->foto ? asset('storage/uploads/karyawan/' . $m->karyawan->foto) : ''
                    ];
                })
            ];
        });

        $formattedMembers = $project->members->map(function ($member) {
            return [
                'nik' => $member->nik,
                'nama' => $member->karyawan ? ($member->karyawan->nama_lengkap ?? $member->karyawan->nama_karyawan) : 'Karyawan',
                'role' => $member->role ?? 'Member',
                'foto' => $member->karyawan && $member->karyawan->foto ? asset('storage/uploads/karyawan/' . $member->karyawan->foto) : ''
            ];
        });

        $leader = $project->members->where('role', 'leader')->first();
        $leaderName = $leader && $leader->karyawan ? ($leader->karyawan->nama_lengkap ?? $leader->karyawan->nama_karyawan) : 'Belum ditentukan';
        $categoryColor = $project->category && $project->category->warna ? $project->category->warna : '#64748b';

        return response()->json([
            'success' => true,
            'data' => [
                'project' => [
                    'id' => $project->id,
                    'kode_project' => $project->kode_project,
                    'nama_project' => $project->nama_project,
                    'deskripsi' => $project->deskripsi ?? '',
                    'category' => $project->category ? $project->category->nama_kategori : 'Uncategorized',
                    'category_color' => $categoryColor,
                    'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : '',
                    'end_date' => $project->end_date ? $project->end_date->format('Y-m-d') : '',
                    'status' => $project->status ?? 'active',
                    'prioritas' => $project->prioritas ?? 'medium',
                    'progress' => (int)($project->progress ?? 0),
                    'leader_name' => $leaderName,
                    'is_leader' => $isLeader,
                    'budget' => $project->budget ? (double)$project->budget : 0.0
                ],
                'tasks' => $formattedTasks,
                'members' => $formattedMembers
            ]
        ]);
    }

    /**
     * Update project status by leader.
     */
    public function updateProjectStatus(Request $request, $id)
    {
        $user = $request->user();
        $nik = $this->getNik($user->id);

        if (empty($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data Karyawan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:planning,in_progress,completed,on_hold,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $project = Project::find($id);
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project tidak ditemukan.'
            ], 404);
        }

        $isLeader = DB::table('project_members')
            ->where('project_id', $project->id)
            ->where('nik', $nik)
            ->where('role', 'leader')
            ->exists();

        if (!$isLeader) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Leader project yang dapat mengubah status project.'
            ], 403);
        }

        $oldStatus = $project->status;
        $newStatus = $request->status;

        $completedAt = $project->completed_at;
        if ($newStatus === 'completed') {
            $completedAt = now();
            $project->progress = 100;
        } else if ($oldStatus === 'completed' && $newStatus !== 'completed') {
            $completedAt = null;
        }

        $project->update([
            'status' => $newStatus,
            'completed_at' => $completedAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status project berhasil diperbarui.',
            'data' => [
                'id' => $project->id,
                'status' => $project->status,
                'progress' => $project->progress
            ]
        ]);
    }

    /**
     * Display the specified task details.
     */
    public function showTask(Request $request, $taskId)
    {
        $user = $request->user();
        $nik = $this->getNik($user->id);

        if (empty($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data Karyawan.'
            ], 404);
        }

        $task = ProjectTask::with([
            'project',
            'parent',
            'subtasks.members.karyawan',
            'members.karyawan',
            'comments.karyawan',
            'attachments.karyawan',
            'logs.karyawan'
        ])->find($taskId);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.'
            ], 404);
        }

        // Verify that user is a member of the project
        $isMember = DB::table('project_members')->where('project_id', $task->project_id)->where('nik', $nik)->exists();
        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke project ini.'
            ], 403);
        }

        $isAssigned = $task->members->contains('nik', $nik);

        $formattedTask = [
            'id' => $task->id,
            'project_id' => $task->project_id,
            'project_name' => $task->project ? $task->project->nama_project : '',
            'parent_id' => $task->parent_id,
            'parent_judul' => $task->parent ? $task->parent->judul : null,
            'kode_task' => $task->kode_task,
            'judul' => $task->judul,
            'deskripsi' => $task->deskripsi ?? '',
            'status' => $task->status ?? 'todo',
            'prioritas' => $task->prioritas ?? 'medium',
            'progress' => (int)($task->progress ?? 0),
            'start_date' => $task->start_date ? $task->start_date->format('Y-m-d') : '',
            'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : '',
            'completed_at' => $task->completed_at ? $task->completed_at->format('Y-m-d H:i:s') : null,
            'is_assigned' => $isAssigned,
            'members' => $task->members->map(function ($m) {
                return [
                    'nik' => $m->nik,
                    'nama' => $m->karyawan ? ($m->karyawan->nama_lengkap ?? $m->karyawan->nama_karyawan) : 'Karyawan',
                    'foto' => $m->karyawan && $m->karyawan->foto ? asset('storage/uploads/karyawan/' . $m->karyawan->foto) : ''
                ];
            }),
            'subtasks' => $task->subtasks->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'kode_task' => $sub->kode_task,
                    'judul' => $sub->judul,
                    'status' => $sub->status,
                    'progress' => (int)($sub->progress ?? 0),
                    'due_date' => $sub->due_date ? $sub->due_date->format('Y-m-d') : ''
                ];
            }),
            'attachments' => $task->attachments->map(function ($att) use ($nik) {
                return [
                    'id' => $att->id,
                    'nama_file' => $att->nama_file,
                    'url' => asset('storage/' . $att->path),
                    'ukuran' => (int)($att->ukuran ?? 0),
                    'tipe_file' => $att->tipe_file ?? '',
                    'uploader_nama' => $att->karyawan ? ($att->karyawan->nama_lengkap ?? $att->karyawan->nama_karyawan) : 'Admin',
                    'is_uploader' => $att->nik === $nik,
                    'created_at' => $att->created_at ? $att->created_at->format('Y-m-d H:i:s') : ''
                ];
            }),
            'comments' => $task->comments->sortByDesc('created_at')->values()->map(function ($com) {
                return [
                    'id' => $com->id,
                    'komentar' => $com->komentar,
                    'nik' => $com->nik,
                    'author_nama' => $com->karyawan ? ($com->karyawan->nama_lengkap ?? $com->karyawan->nama_karyawan) : 'Admin',
                    'author_foto' => $com->karyawan && $com->karyawan->foto ? asset('storage/uploads/karyawan/' . $com->karyawan->foto) : '',
                    'created_at' => $com->created_at ? $com->created_at->diffForHumans() : '',
                    'created_at_raw' => $com->created_at ? $com->created_at->format('Y-m-d H:i:s') : ''
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $formattedTask
        ]);
    }

    /**
     * Store comment on a task.
     */
    public function storeComment(Request $request, $taskId)
    {
        $user = $request->user();
        $nik = $this->getNik($user->id);

        if (empty($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data Karyawan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'komentar' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $task = ProjectTask::find($taskId);
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.'
            ], 404);
        }

        // Verify membership
        $isMember = DB::table('project_members')->where('project_id', $task->project_id)->where('nik', $nik)->exists();
        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke project ini.'
            ], 403);
        }

        $comment = ProjectTaskComment::create([
            'task_id' => $task->id,
            'nik' => $nik,
            'komentar' => $request->komentar,
        ]);

        // Log activity
        ProjectTaskLog::create([
            'task_id' => $task->id,
            'nik' => $nik,
            'aksi' => 'comment_added',
            'keterangan' => 'Menambahkan komentar pada task.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dikirim.',
            'data' => [
                'id' => $comment->id,
                'komentar' => $comment->komentar,
                'created_at' => $comment->created_at ? $comment->created_at->diffForHumans() : 'baru saja'
            ]
        ]);
    }

    /**
     * Store attachment on a task.
     */
    public function storeAttachment(Request $request, $taskId)
    {
        $user = $request->user();
        $nik = $this->getNik($user->id);

        if (empty($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data Karyawan.'
            ], 404);
        }

        $task = ProjectTask::find($taskId);
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.'
            ], 404);
        }

        // Verify assignment
        $isAssigned = DB::table('project_task_members')->where('task_id', $task->id)->where('nik', $nik)->exists();
        if (!$isAssigned) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya anggota yang ditugaskan yang dapat mengunggah lampiran.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Store to public disk
        $path = $file->store("project_tasks/{$task->id}", 'public');

        $attachment = ProjectTaskAttachment::create([
            'task_id' => $task->id,
            'nik' => $nik,
            'nama_file' => $originalName,
            'path' => $path,
            'ukuran' => $fileSize,
            'tipe_file' => $mimeType,
        ]);

        // Log activity
        ProjectTaskLog::create([
            'task_id' => $task->id,
            'nik' => $nik,
            'aksi' => 'attachment_added',
            'keterangan' => "Mengunggah lampiran: $originalName",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lampiran berhasil diunggah.',
            'data' => [
                'id' => $attachment->id,
                'nama_file' => $attachment->nama_file,
                'url' => asset('storage/' . $attachment->path),
                'ukuran' => $attachment->ukuran,
                'tipe_file' => $attachment->tipe_file
            ]
        ]);
    }

    /**
     * Delete attachment.
     */
    public function deleteAttachment(Request $request, $attachmentId)
    {
        $user = $request->user();
        $nik = $this->getNik($user->id);

        if (empty($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data Karyawan.'
            ], 404);
        }

        $attachment = ProjectTaskAttachment::find($attachmentId);
        if (!$attachment) {
            return response()->json([
                'success' => false,
                'message' => 'Lampiran tidak ditemukan.'
            ], 404);
        }

        // Check if user is uploader or leader of the project
        $task = ProjectTask::find($attachment->task_id);
        $isLeader = false;
        if ($task) {
            $isLeader = DB::table('project_members')
                ->where('project_id', $task->project_id)
                ->where('nik', $nik)
                ->where('role', 'leader')
                ->exists();
        }

        if ($attachment->nik !== $nik && !$isLeader) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak untuk menghapus lampiran ini.'
            ], 403);
        }

        if (Storage::disk('public')->exists($attachment->path)) {
            Storage::disk('public')->delete($attachment->path);
        }

        $fileName = $attachment->nama_file;
        $taskId = $attachment->task_id;
        $attachment->delete();

        ProjectTaskLog::create([
            'task_id' => $taskId,
            'nik' => $nik,
            'aksi' => 'attachment_deleted',
            'keterangan' => "Menghapus lampiran: $fileName",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lampiran berhasil dihapus.'
        ]);
    }

    /**
     * Update task status from mobile board.
     */
    public function updateTaskStatus(Request $request, $taskId)
    {
        $user = $request->user();
        $nik = $this->getNik($user->id);

        if (empty($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data Karyawan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:todo,in_progress,review,completed,cancelled',
            'progress' => 'nullable|integer|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $task = ProjectTask::find($taskId);
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.'
            ], 404);
        }

        // Verify that the user is a member of the task's project
        $isMember = DB::table('project_members')->where('project_id', $task->project_id)->where('nik', $nik)->exists();
        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke project ini.'
            ], 403);
        }

        // Verify that the user is assigned to this specific task
        $isAssigned = DB::table('project_task_members')->where('task_id', $task->id)->where('nik', $nik)->exists();
        if (!$isAssigned) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak ditugaskan pada task ini sehingga tidak dapat memperbarui progress.'
            ], 403);
        }

        $oldStatus = $task->status;
        $newStatus = $request->status;

        $updateData = ['status' => $newStatus];
        
        if ($newStatus === 'completed') {
            $updateData['progress'] = 100;
            $updateData['completed_at'] = now();
        } else {
            if ($request->has('progress')) {
                $updateData['progress'] = $request->progress;
            }
            if ($oldStatus === 'completed') {
                $updateData['completed_at'] = null;
            }
        }

        DB::beginTransaction();
        try {
            $task->update($updateData);

            // Recalculate project progress
            $project = Project::find($task->project_id);
            if ($project) {
                $project->calculateProgress();
            }

            // Log the change
            DB::table('project_task_logs')->insert([
                'task_id' => $task->id,
                'nik' => $nik,
                'aksi' => 'status_updated',
                'data_lama' => json_encode(['status' => $oldStatus]),
                'data_baru' => json_encode(['status' => $newStatus]),
                'keterangan' => "Mengubah status task dari '$oldStatus' menjadi '$newStatus'",
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status task berhasil diperbarui.',
                'data' => [
                    'task_id' => $task->id,
                    'status' => $task->status,
                    'progress' => $task->progress,
                    'project_progress' => $project ? $project->progress : 0
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status task: ' . $e->getMessage()
            ], 500);
        }
    }
}

