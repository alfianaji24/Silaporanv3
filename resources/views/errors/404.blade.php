<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 Page Not Found</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script src="/_sdk/element_sdk.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(2deg); }
    }

    @keyframes pulse-glow {
      0%, 100% { opacity: 0.4; }
      50% { opacity: 0.9; }
    }

    @keyframes search-shake {
      0%, 100% { transform: translateX(0) rotate(-5deg); }
      25% { transform: translateX(-8px) rotate(-4deg); }
      75% { transform: translateX(8px) rotate(-6deg); }
    }

    @keyframes bounce-item {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-12px); }
    }

    @keyframes fade-in {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slide-down {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse-ring {
      0% { r: 30px; opacity: 1; }
      100% { r: 50px; opacity: 0; }
    }

    .float-animation {
      animation: float 4s ease-in-out infinite;
    }

    .pulse-glow {
      animation: pulse-glow 2s ease-in-out infinite;
    }

    .search-shake {
      animation: search-shake 2s ease-in-out infinite;
      transform-origin: center;
    }

    .bounce-item {
      animation: bounce-item 2s ease-in-out infinite;
    }

    .bounce-item-2 {
      animation: bounce-item 2.2s ease-in-out infinite 0.3s;
    }

    .bounce-item-3 {
      animation: bounce-item 2.4s ease-in-out infinite 0.6s;
    }

    .fade-in {
      animation: fade-in 0.8s ease-out forwards;
    }

    .slide-down {
      animation: slide-down 0.8s ease-out forwards;
    }

    .pulse-ring {
      animation: pulse-ring 2s ease-out infinite;
    }
  </style>
</head>
<body class="h-full overflow-auto">
  <div id="app-container" class="min-h-full w-full flex items-center justify-center p-8" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);">

    <!-- Background decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <!-- Floating circles -->
      <div class="absolute top-20 right-10 w-40 h-40 rounded-full pulse-glow" style="background: radial-gradient(circle, rgba(248, 113, 113, 0.2) 0%, transparent 70%);"></div>
      <div class="absolute bottom-20 left-10 w-48 h-48 rounded-full pulse-glow" style="background: radial-gradient(circle, rgba(251, 191, 36, 0.15) 0%, transparent 70%); animation-delay: 1.5s;"></div>

      <!-- Grid pattern -->
      <svg class="absolute inset-0 w-full h-full opacity-5" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <pattern id="grid-404" width="50" height="50" patternUnits="userSpaceOnUse">
            <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#f87171" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid-404)" />
      </svg>
    </div>

    <div class="relative z-10 text-center max-w-2xl mx-auto">

      <!-- Main illustration -->
      <div class="float-animation mb-8">
        <svg viewBox="0 0 400 320" class="w-full max-w-md mx-auto" xmlns="http://www.w3.org/2000/svg">
          <!-- Ground -->
          <ellipse cx="200" cy="290" rx="180" ry="20" fill="#ffffff" opacity="0.05"/>

          <!-- Magnifying glass (searching) -->
          <g class="search-shake" style="transform-origin: 240px 100px;">
            <!-- Handle -->
            <line x1="250" y1="130" x2="300" y2="180" stroke="#ef4444" stroke-width="8" stroke-linecap="round"/>
            <!-- Glass -->
            <circle cx="240" cy="100" r="50" fill="none" stroke="#ef4444" stroke-width="6"/>
            <!-- Crosshairs inside -->
            <line x1="240" y1="60" x2="240" y2="140" stroke="#fbbf24" stroke-width="1.5" opacity="0.6"/>
            <line x1="190" y1="100" x2="290" y2="100" stroke="#fbbf24" stroke-width="1.5" opacity="0.6"/>
            <!-- Inner circle -->
            <circle cx="240" cy="100" r="30" fill="none" stroke="#ef4444" stroke-width="2" opacity="0.5"/>
          </g>

          <!-- Question marks floating -->
          <g class="bounce-item" style="transform-origin: 100px 180px;">
            <text x="100" y="180" font-family="'JetBrains Mono', monospace" font-size="48" font-weight="bold" fill="#ef4444" opacity="0.3">?</text>
          </g>

          <g class="bounce-item-2" style="transform-origin: 320px 200px;">
            <text x="320" y="200" font-family="'JetBrains Mono', monospace" font-size="40" font-weight="bold" fill="#fbbf24" opacity="0.3">?</text>
          </g>

          <g class="bounce-item-3" style="transform-origin: 60px 90px;">
            <text x="60" y="90" font-family="'JetBrains Mono', monospace" font-size="36" font-weight="bold" fill="#ef4444" opacity="0.25">?</text>
          </g>

          <!-- Lost page icon -->
          <g style="transform: translate(200px, 180px);">
            <!-- Page/Document -->
            <rect x="-25" y="-40" width="50" height="70" rx="5" fill="none" stroke="#94a3b8" stroke-width="2.5"/>
            <!-- Page lines -->
            <line x1="-20" y1="-30" x2="20" y2="-30" stroke="#94a3b8" stroke-width="1.5" opacity="0.5"/>
            <line x1="-20" y1="-20" x2="20" y2="-20" stroke="#94a3b8" stroke-width="1.5" opacity="0.5"/>
            <line x1="-20" y1="-10" x2="20" y2="-10" stroke="#94a3b8" stroke-width="1.5" opacity="0.5"/>
            <line x1="-20" y1="0" x2="20" y2="0" stroke="#94a3b8" stroke-width="1.5" opacity="0.5"/>
            <!-- Broken symbol (X) -->
            <line x1="-8" y1="15" x2="8" y2="28" stroke="#ef4444" stroke-width="3" stroke-linecap="round"/>
            <line x1="8" y1="15" x2="-8" y2="28" stroke="#ef4444" stroke-width="3" stroke-linecap="round"/>
          </g>

          <!-- Search path traces -->
          <path d="M 240 70 Q 150 50 80 150" fill="none" stroke="#22d3ee" stroke-width="2" stroke-dasharray="5,5" opacity="0.3"/>
          <path d="M 280 120 Q 300 180 200 260" fill="none" stroke="#22d3ee" stroke-width="2" stroke-dasharray="5,5" opacity="0.3"/>

          <!-- Compass/Direction indicator -->
          <g style="transform: translate(80px, 50px);">
            <circle cx="0" cy="0" r="18" fill="none" stroke="#fbbf24" stroke-width="1.5" opacity="0.4"/>
            <line x1="0" y1="-15" x2="0" y2="-22" stroke="#fbbf24" stroke-width="2" opacity="0.4" stroke-linecap="round"/>
            <line x1="0" y1="15" x2="0" y2="22" stroke="#fbbf24" stroke-width="1" opacity="0.3" stroke-linecap="round"/>
            <line x1="-15" y1="0" x2="-22" y2="0" stroke="#fbbf24" stroke-width="1" opacity="0.3" stroke-linecap="round"/>
            <line x1="15" y1="0" x2="22" y2="0" stroke="#fbbf24" stroke-width="2" opacity="0.4" stroke-linecap="round"/>
          </g>
        </svg>
      </div>

      <!-- Error code -->
      <h1 id="error-title" class="text-8xl md:text-9xl font-bold mb-4 tracking-tight slide-down" style="font-family: 'JetBrains Mono', monospace; background: linear-gradient(135deg, #ef4444 0%, #fbbf24 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
        404
      </h1>

      <!-- Subtitle -->
      <h2 id="error-subtitle" class="text-2xl md:text-3xl font-semibold mb-6 fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #e2e8f0; animation-delay: 0.2s;">
        Page Not Found
      </h2>

      <!-- Message -->
      <p id="error-message" class="text-lg mb-10 max-w-md mx-auto leading-relaxed fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #cbd5e1; animation-delay: 0.3s;">
        The page you're looking for doesn't exist or has been moved to a different location.
      </p>

      <!-- Action button -->
      <button id="retry-button" onclick="window.location.href='/'" class="group relative px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 active:scale-95 fade-in" style="font-family: 'Space Grotesk', sans-serif; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; box-shadow: 0 10px 40px -10px rgba(239, 68, 68, 0.5); animation-delay: 0.4s;">
        <span class="relative z-10 flex items-center gap-3">
          <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1v-10"/>
          </svg>
          Go Home
        </span>
      </button>

      <!-- Navigation tips -->
      <div class="mt-10 flex flex-col gap-2" style="color: #64748b;">
        <div class="fade-in" style="animation-delay: 0.5s;">
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px;">🔍 Try using the search to find what you need</span>
        </div>
        <div class="fade-in" style="animation-delay: 0.6s;">
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px;">🗺️ Or check the navigation menu for more options</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    const defaultConfig = {
      error_title: '404',
      error_subtitle: 'Page Not Found',
      error_message: "The page you're looking for doesn't exist or has been moved to a different location.",
      button_text: 'Go Home',
      background_color: '#0f172a',
      text_color: '#e2e8f0',
      accent_color: '#ef4444',
      secondary_color: '#fbbf24',
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
        button.style.background = `linear-gradient(135deg, ${accentColor} 0%, ${adjustColor(accentColor, -30)} 100%)`;
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
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9cfb4906d5509a10',t:'MTc3MTM5NDIyOC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
