<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>503 Service Unavailable</title>
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
      0%, 100% { opacity: 0.4; transform: scale(1); }
      50% { opacity: 0.8; transform: scale(1.05); }
    }

    @keyframes spin-slow {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    @keyframes dash {
      0% { stroke-dashoffset: 0; }
      100% { stroke-dashoffset: 100; }
    }

    @keyframes bounce-subtle {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-5px); }
    }

    .float-animation {
      animation: float 4s ease-in-out infinite;
    }

    .pulse-glow {
      animation: pulse-glow 3s ease-in-out infinite;
    }

    .spin-slow {
      animation: spin-slow 20s linear infinite;
    }

    .dash-animation {
      stroke-dasharray: 10 5;
      animation: dash 2s linear infinite;
    }

    .bounce-animation {
      animation: bounce-subtle 2s ease-in-out infinite;
    }

    .gear-spin {
      animation: spin-slow 8s linear infinite;
      transform-origin: center;
    }

    .gear-spin-reverse {
      animation: spin-slow 6s linear infinite reverse;
      transform-origin: center;
    }
  </style>
</head>
<body class="h-full overflow-auto">
  <div id="app-container" class="min-h-full w-full flex items-center justify-center p-8" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);">

    <!-- Background decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <!-- Floating circles -->
      <div class="absolute top-20 left-10 w-32 h-32 rounded-full pulse-glow" style="background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, transparent 70%);"></div>
      <div class="absolute bottom-20 right-10 w-48 h-48 rounded-full pulse-glow" style="background: radial-gradient(circle, rgba(236, 72, 153, 0.2) 0%, transparent 70%); animation-delay: 1s;"></div>
      <div class="absolute top-1/3 right-1/4 w-24 h-24 rounded-full pulse-glow" style="background: radial-gradient(circle, rgba(34, 211, 238, 0.2) 0%, transparent 70%); animation-delay: 2s;"></div>

      <!-- Grid pattern -->
      <svg class="absolute inset-0 w-full h-full opacity-10" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#6366f1" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" />
      </svg>
    </div>

    <div class="relative z-10 text-center max-w-2xl mx-auto">

      <!-- Main illustration -->
      <div class="float-animation mb-8">
        <svg viewBox="0 0 400 300" class="w-full max-w-md mx-auto" xmlns="http://www.w3.org/2000/svg">
          <!-- Server rack -->
          <rect x="120" y="80" width="160" height="180" rx="10" fill="#1e293b" stroke="#334155" stroke-width="2"/>
          <rect x="130" y="90" width="140" height="40" rx="5" fill="#0f172a" stroke="#475569" stroke-width="1"/>
          <rect x="130" y="140" width="140" height="40" rx="5" fill="#0f172a" stroke="#475569" stroke-width="1"/>
          <rect x="130" y="190" width="140" height="40" rx="5" fill="#0f172a" stroke="#475569" stroke-width="1"/>

          <!-- Server lights - blinking red -->
          <circle cx="150" cy="110" r="5" fill="#ef4444" class="pulse-glow"/>
          <circle cx="165" cy="110" r="5" fill="#ef4444" class="pulse-glow" style="animation-delay: 0.5s;"/>
          <circle cx="150" cy="160" r="5" fill="#ef4444" class="pulse-glow" style="animation-delay: 1s;"/>
          <circle cx="165" cy="160" r="5" fill="#fbbf24" class="pulse-glow" style="animation-delay: 0.3s;"/>
          <circle cx="150" cy="210" r="5" fill="#ef4444" class="pulse-glow" style="animation-delay: 0.7s;"/>
          <circle cx="165" cy="210" r="5" fill="#ef4444" class="pulse-glow"/>

          <!-- Vent lines -->
          <line x1="200" y1="100" x2="260" y2="100" stroke="#475569" stroke-width="2"/>
          <line x1="200" y1="108" x2="260" y2="108" stroke="#475569" stroke-width="2"/>
          <line x1="200" y1="116" x2="260" y2="116" stroke="#475569" stroke-width="2"/>
          <line x1="200" y1="150" x2="260" y2="150" stroke="#475569" stroke-width="2"/>
          <line x1="200" y1="158" x2="260" y2="158" stroke="#475569" stroke-width="2"/>
          <line x1="200" y1="166" x2="260" y2="166" stroke="#475569" stroke-width="2"/>
          <line x1="200" y1="200" x2="260" y2="200" stroke="#475569" stroke-width="2"/>
          <line x1="200" y1="208" x2="260" y2="208" stroke="#475569" stroke-width="2"/>
          <line x1="200" y1="216" x2="260" y2="216" stroke="#475569" stroke-width="2"/>

          <!-- Gears (maintenance) -->
          <g class="gear-spin" style="transform-origin: 330px 100px;">
            <circle cx="330" cy="100" r="25" fill="none" stroke="#6366f1" stroke-width="4"/>
            <circle cx="330" cy="100" r="8" fill="#6366f1"/>
            <path d="M330 70 L330 75 M330 125 L330 130 M300 100 L305 100 M355 100 L360 100 M309 79 L313 83 M347 117 L351 121 M309 121 L313 117 M347 83 L351 79" stroke="#6366f1" stroke-width="4" stroke-linecap="round"/>
          </g>

          <g class="gear-spin-reverse" style="transform-origin: 70px 180px;">
            <circle cx="70" cy="180" r="20" fill="none" stroke="#ec4899" stroke-width="3"/>
            <circle cx="70" cy="180" r="6" fill="#ec4899"/>
            <path d="M70 156 L70 160 M70 200 L70 204 M46 180 L50 180 M90 180 L94 180 M53 163 L56 166 M84 194 L87 197 M53 197 L56 194 M84 166 L87 163" stroke="#ec4899" stroke-width="3" stroke-linecap="round"/>
          </g>

          <!-- Wrench -->
          <g class="bounce-animation" style="transform-origin: 320px 200px;">
            <path d="M300 220 L340 180" stroke="#fbbf24" stroke-width="6" stroke-linecap="round"/>
            <circle cx="345" cy="175" r="12" fill="none" stroke="#fbbf24" stroke-width="4"/>
            <path d="M340 170 L350 180 M350 170 L340 180" stroke="#fbbf24" stroke-width="3"/>
          </g>

          <!-- Connection cables (broken) -->
          <path d="M120 130 Q80 130 80 150 Q80 170 50 170" stroke="#22d3ee" stroke-width="3" fill="none" class="dash-animation"/>
          <circle cx="50" cy="170" r="6" fill="#22d3ee" class="pulse-glow"/>

          <!-- X mark indicating disconnection -->
          <g transform="translate(60, 145)">
            <line x1="-8" y1="-8" x2="8" y2="8" stroke="#ef4444" stroke-width="3" stroke-linecap="round"/>
            <line x1="8" y1="-8" x2="-8" y2="8" stroke="#ef4444" stroke-width="3" stroke-linecap="round"/>
          </g>

          <!-- Status bar -->
          <rect x="140" y="240" width="120" height="10" rx="5" fill="#1e293b" stroke="#334155"/>
          <rect x="142" y="242" width="40" height="6" rx="3" fill="#fbbf24" class="pulse-glow"/>
        </svg>
      </div>

      <!-- Error code -->
      <h1 id="error-title" class="text-8xl md:text-9xl font-bold mb-4 tracking-tight" style="font-family: 'JetBrains Mono', monospace; background: linear-gradient(135deg, #6366f1 0%, #ec4899 50%, #22d3ee 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
        503
      </h1>

      <!-- Subtitle -->
      <h2 id="error-subtitle" class="text-2xl md:text-3xl font-semibold mb-6" style="font-family: 'Space Grotesk', sans-serif; color: #e2e8f0;">
        Service Unavailable
      </h2>

      <!-- Message -->
      <p id="error-message" class="text-lg mb-10 max-w-md mx-auto leading-relaxed" style="font-family: 'Space Grotesk', sans-serif; color: #94a3b8;">
        We're currently performing maintenance to improve your experience. Please check back in a few minutes.
      </p>

      <!-- Action button -->
      <button id="retry-button" onclick="window.location.reload()" class="group relative px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 active:scale-95" style="font-family: 'Space Grotesk', sans-serif; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #ffffff; box-shadow: 0 10px 40px -10px rgba(99, 102, 241, 0.5);">
        <span class="relative z-10 flex items-center gap-3">
          <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Try Again
        </span>
      </button>

      <!-- Estimated time -->
      <div class="mt-10 flex items-center justify-center gap-2" style="color: #64748b;">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span style="font-family: 'Space Grotesk', sans-serif; font-size: 14px;">Estimated downtime: ~5 minutes</span>
      </div>
    </div>
  </div>

  <script>
    const defaultConfig = {
      error_title: '503',
      error_subtitle: 'Service Unavailable',
      error_message: "We're currently performing maintenance to improve your experience. Please check back in a few minutes.",
      button_text: 'Try Again',
      background_color: '#0f172a',
      text_color: '#e2e8f0',
      accent_color: '#6366f1',
      secondary_color: '#ec4899',
      muted_color: '#94a3b8',
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

      if (container) container.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${adjustColor(bgColor, 20)} 50%, ${bgColor} 100%)`;
      if (subtitle) subtitle.style.color = textColor;
      if (message) message.style.color = mutedColor;
      if (title) {
        title.style.background = `linear-gradient(135deg, ${accentColor} 0%, ${secondaryColor} 50%, #22d3ee 100%)`;
        title.style.webkitBackgroundClip = 'text';
        title.style.backgroundClip = 'text';
      }
      if (button) {
        button.style.background = `linear-gradient(135deg, ${accentColor} 0%, ${adjustColor(accentColor, 30)} 100%)`;
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
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9cfb42da96039a10',t:'MTc3MTM5Mzk3NS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
