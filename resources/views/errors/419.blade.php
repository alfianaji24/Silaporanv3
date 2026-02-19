<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>419 Session Expired</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script src="/_sdk/element_sdk.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }

    @keyframes fade-in {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slide-down {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse-glow {
      0%, 100% { opacity: 0.4; }
      50% { opacity: 0.9; }
    }

    @keyframes rotate-clock {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    @keyframes sand-fall {
      0% { transform: translateY(-100%); opacity: 1; }
      90% { opacity: 1; }
      100% { transform: translateY(80px); opacity: 0; }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-15px); }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-3px); }
      75% { transform: translateX(3px); }
    }

    .fade-in {
      animation: fade-in 0.8s ease-out forwards;
    }

    .slide-down {
      animation: slide-down 0.8s ease-out forwards;
    }

    .pulse-glow {
      animation: pulse-glow 2s ease-in-out infinite;
    }

    .float-animation {
      animation: float 4s ease-in-out infinite;
    }

    .rotate-clock {
      animation: rotate-clock 20s linear infinite;
    }

    .sand-fall-1 { animation: sand-fall 2s ease-in infinite; }
    .sand-fall-2 { animation: sand-fall 2.5s ease-in infinite 0.3s; }
    .sand-fall-3 { animation: sand-fall 3s ease-in infinite 0.6s; }
    .sand-fall-4 { animation: sand-fall 2.2s ease-in infinite 0.1s; }

    .shake-animation {
      animation: shake 0.5s ease-in-out infinite 3s;
    }
  </style>
</head>
<body class="h-full overflow-auto">
  <div id="app-container" class="min-h-full w-full flex items-center justify-center p-8" style="background: linear-gradient(135deg, #1a1f2e 0%, #2d3748 50%, #1a1f2e 100%);">

    <!-- Background decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <!-- Floating circles -->
      <div class="absolute top-20 right-10 w-40 h-40 rounded-full pulse-glow" style="background: radial-gradient(circle, rgba(168, 85, 247, 0.2) 0%, transparent 70%);"></div>
      <div class="absolute bottom-20 left-10 w-48 h-48 rounded-full pulse-glow" style="background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%); animation-delay: 1.5s;"></div>

      <!-- Grid pattern -->
      <svg class="absolute inset-0 w-full h-full opacity-5" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <pattern id="grid-419" width="50" height="50" patternUnits="userSpaceOnUse">
            <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#a855f7" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid-419)" />
      </svg>
    </div>

    <div class="relative z-10 text-center max-w-2xl mx-auto">

      <!-- Main illustration -->
      <div class="float-animation mb-8">
        <svg viewBox="0 0 400 300" class="w-full max-w-md mx-auto" xmlns="http://www.w3.org/2000/svg">
          <!-- Hourglass body -->
          <g>
            <!-- Top chamber -->
            <path d="M 100 50 L 140 100 L 260 100 L 300 50 Z" fill="#a855f7" opacity="0.1" stroke="#a855f7" stroke-width="2"/>
            <path d="M 100 50 L 300 50 L 300 60 L 100 60 Z" fill="#a855f7" stroke="#a855f7" stroke-width="2"/>

            <!-- Sand falling from top -->
            <rect x="195" y="55" width="10" height="8" fill="#fbbf24" class="sand-fall-1"/>
            <rect x="185" y="70" width="8" height="8" fill="#fbbf24" class="sand-fall-2" opacity="0.8"/>
            <rect x="205" y="75" width="8" height="8" fill="#fbbf24" class="sand-fall-3" opacity="0.7"/>
            <rect x="192" y="60" width="10" height="8" fill="#fbbf24" class="sand-fall-4"/>

            <!-- Narrow middle -->
            <line x1="155" y1="100" x2="175" y2="150" stroke="#a855f7" stroke-width="3"/>
            <line x1="245" y1="100" x2="225" y2="150" stroke="#a855f7" stroke-width="3"/>

            <!-- Bottom chamber -->
            <path d="M 140 150 L 100 200 L 300 200 L 260 150 Z" fill="#a855f7" opacity="0.1" stroke="#a855f7" stroke-width="2"/>
            <path d="M 100 200 L 300 200 L 300 210 L 100 210 Z" fill="#a855f7" stroke="#a855f7" stroke-width="2"/>

            <!-- Sand in bottom -->
            <path d="M 130 200 L 170 160 L 230 160 L 270 200 Z" fill="#fbbf24" opacity="0.6"/>

            <!-- Decorative lines on glass -->
            <line x1="110" y1="70" x2="115" y2="70" stroke="#a855f7" stroke-width="1" opacity="0.5"/>
            <line x1="290" y1="70" x2="285" y2="70" stroke="#a855f7" stroke-width="1" opacity="0.5"/>
            <line x1="115" y1="180" x2="120" y2="180" stroke="#a855f7" stroke-width="1" opacity="0.5"/>
            <line x1="280" y1="180" x2="285" y2="180" stroke="#a855f7" stroke-width="1" opacity="0.5"/>
          </g>

          <!-- Gears (time expired) -->
          <g class="rotate-clock" style="transform-origin: 340px 80px;">
            <circle cx="340" cy="80" r="22" fill="none" stroke="#ef4444" stroke-width="3"/>
            <circle cx="340" cy="80" r="6" fill="#ef4444"/>
            <!-- Gear teeth -->
            <path d="M340 56 L340 52 M340 108 L340 104 M316 80 L312 80 M364 80 L368 80 M321 61 L318 58 M362 102 L365 105 M321 99 L318 102 M362 58 L365 55" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round"/>
          </g>

          <!-- Lock icon overlay -->
          <g style="transform: translateY(140px); transform-origin: 200px 0;">
            <rect x="175" y="130" width="50" height="45" rx="5" fill="none" stroke="#22d3ee" stroke-width="2.5"/>
            <path d="M 185 145 Q 185 140 200 140 Q 215 140 215 145 L 185 145" fill="none" stroke="#22d3ee" stroke-width="2.5"/>
            <circle cx="200" cy="160" r="3" fill="#22d3ee"/>
            <line x1="200" y1="163" x2="200" y2="168" stroke="#22d3ee" stroke-width="2" stroke-linecap="round"/>
          </g>

          <!-- Pulsing warning circle -->
          <circle cx="200" cy="240" r="12" fill="none" stroke="#ef4444" stroke-width="2" class="pulse-glow"/>
          <circle cx="200" cy="240" r="6" fill="#ef4444"/>
        </svg>
      </div>

      <!-- Error code -->
      <h1 id="error-title" class="text-8xl md:text-9xl font-bold mb-4 tracking-tight slide-down" style="font-family: 'JetBrains Mono', monospace; background: linear-gradient(135deg, #a855f7 0%, #ef4444 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
        419
      </h1>

      <!-- Subtitle -->
      <h2 id="error-subtitle" class="text-2xl md:text-3xl font-semibold mb-6 fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #e2e8f0; animation-delay: 0.2s;">
        Session Expired
      </h2>

      <!-- Message -->
      <p id="error-message" class="text-lg mb-10 max-w-md mx-auto leading-relaxed fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #cbd5e1; animation-delay: 0.3s;">
        Your session has expired due to inactivity. Please log in again to continue.
      </p>

      <!-- Action button -->
      <button id="retry-button" onclick="window.location.reload()" class="group relative px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 active:scale-95 fade-in" style="font-family: 'Space Grotesk', sans-serif; background: linear-gradient(135deg, #a855f7 0%, #d946ef 100%); color: #ffffff; box-shadow: 0 10px 40px -10px rgba(168, 85, 247, 0.5); animation-delay: 0.4s;">
        <span class="relative z-10 flex items-center gap-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
          </svg>
          Login Again
        </span>
      </button>

      <!-- Additional info -->
      <div class="mt-10 flex flex-col gap-3" style="color: #64748b;">
        <div class="flex items-center justify-center gap-2 fade-in" style="animation-delay: 0.5s;">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 14px;">Session timeout: 30 minutes of inactivity</span>
        </div>
        <div class="flex items-center justify-center gap-2 fade-in" style="animation-delay: 0.6s;">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 14px;">Your data is safely secured</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    const defaultConfig = {
      error_title: '419',
      error_subtitle: 'Session Expired',
      error_message: 'Your session has expired due to inactivity. Please log in again to continue.',
      button_text: 'Login Again',
      background_color: '#1a1f2e',
      text_color: '#e2e8f0',
      accent_color: '#a855f7',
      secondary_color: '#ef4444',
      muted_color: '#cbd5e1',
      font_family: 'Space Grotesk',
      font_size: 16
    };

    async function onConfigChange(config) {
      const title = document.getElementById('error-title');
      const subtitle = document.getElementById('error-subtitle');
      const message = document.getElementById('error-message');
      const button = document.getElementById('retry-button');
      const container = document.getElementById('app-container');

      if (title) title.textContent = config.error_title || defaultConfig.error_title;
      if (subtitle) subtitle.textContent = config.error_subtitle || defaultConfig.error_subtitle;
      if (message) message.textContent = config.error_message || defaultConfig.error_message;
      if (button) button.querySelector('span').lastChild.textContent = config.button_text || defaultConfig.button_text;

      const bgColor = config.background_color || defaultConfig.background_color;
      const textColor = config.text_color || defaultConfig.text_color;
      const accentColor = config.accent_color || defaultConfig.accent_color;
      const secondaryColor = config.secondary_color || defaultConfig.secondary_color;
      const mutedColor = config.muted_color || defaultConfig.muted_color;

      if (container) container.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${adjustColor(bgColor, 30)} 50%, ${bgColor} 100%)`;
      if (subtitle) subtitle.style.color = textColor;
      if (message) message.style.color = mutedColor;
      if (title) {
        title.style.background = `linear-gradient(135deg, ${accentColor} 0%, ${secondaryColor} 100%)`;
        title.style.webkitBackgroundClip = 'text';
        title.style.backgroundClip = 'text';
      }
      if (button) {
        button.style.background = `linear-gradient(135deg, ${accentColor} 0%, ${adjustColor(accentColor, 40)} 100%)`;
        button.style.boxShadow = `0 10px 40px -10px ${accentColor}80`;
      }

      const fontFamily = config.font_family || defaultConfig.font_family;
      const fontSize = config.font_size || defaultConfig.font_size;

      if (subtitle) subtitle.style.fontFamily = `'${fontFamily}', sans-serif`;
      if (message) {
        message.style.fontFamily = `'${fontFamily}', sans-serif`;
        message.style.fontSize = `${fontSize * 1.125}px`;
      }
      if (button) button.style.fontFamily = `'${fontFamily}', sans-serif`;
    }

    function adjustColor(hex, amount) {
      const num = parseInt(hex.replace('#', ''), 16);
      const r = Math.min(255, Math.max(0, (num >> 16) + amount));
      const g = Math.min(255, Math.max(0, ((num >> 8) & 0x00FF) + amount));
      const b = Math.min(255, Math.max(0, (num & 0x0000FF) + amount));
      return `#${(1 << 24 | r << 16 | g << 8 | b).toString(16).slice(1)}`;
    }

    function mapToCapabilities(config) {
      return {
        recolorables: [
          {
            get: () => config.background_color || defaultConfig.background_color,
            set: (value) => { config.background_color = value; window.elementSdk.setConfig({ background_color: value }); }
          },
          {
            get: () => config.text_color || defaultConfig.text_color,
            set: (value) => { config.text_color = value; window.elementSdk.setConfig({ text_color: value }); }
          },
          {
            get: () => config.accent_color || defaultConfig.accent_color,
            set: (value) => { config.accent_color = value; window.elementSdk.setConfig({ accent_color: value }); }
          },
          {
            get: () => config.secondary_color || defaultConfig.secondary_color,
            set: (value) => { config.secondary_color = value; window.elementSdk.setConfig({ secondary_color: value }); }
          },
          {
            get: () => config.muted_color || defaultConfig.muted_color,
            set: (value) => { config.muted_color = value; window.elementSdk.setConfig({ muted_color: value }); }
          }
        ],
        borderables: [],
        fontEditable: {
          get: () => config.font_family || defaultConfig.font_family,
          set: (value) => { config.font_family = value; window.elementSdk.setConfig({ font_family: value }); }
        },
        fontSizeable: {
          get: () => config.font_size || defaultConfig.font_size,
          set: (value) => { config.font_size = value; window.elementSdk.setConfig({ font_size: value }); }
        }
      };
    }

    function mapToEditPanelValues(config) {
      return new Map([
        ['error_title', config.error_title || defaultConfig.error_title],
        ['error_subtitle', config.error_subtitle || defaultConfig.error_subtitle],
        ['error_message', config.error_message || defaultConfig.error_message],
        ['button_text', config.button_text || defaultConfig.button_text]
      ]);
    }

    if (window.elementSdk) {
      window.elementSdk.init({
        defaultConfig,
        onConfigChange,
        mapToCapabilities,
        mapToEditPanelValues
      });
    } else {
      onConfigChange(defaultConfig);
    }
  </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9cfb456756f79a10',t:'MTc3MTM5NDA4MC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
