<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>502 Bad Gateway</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script src="/_sdk/element_sdk.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(1deg); }
    }
    
    @keyframes pulse-glow {
      0%, 100% { opacity: 0.4; }
      50% { opacity: 0.9; }
    }
    
    @keyframes bridge-sway {
      0%, 100% { transform: rotateZ(0deg); }
      25% { transform: rotateZ(-2deg); }
      75% { transform: rotateZ(2deg); }
    }
    
    @keyframes cable-wave {
      0%, 100% { stroke-dashoffset: 0; }
      50% { stroke-dashoffset: 20; }
    }
    
    @keyframes connection-pulse {
      0%, 100% { opacity: 0.3; }
      50% { opacity: 1; }
    }
    
    @keyframes server-blink {
      0%, 100% { fill: #94a3b8; }
      50% { fill: #ef4444; }
    }
    
    @keyframes data-flow {
      0% { transform: translateX(-100%); opacity: 0; }
      10% { opacity: 1; }
      90% { opacity: 1; }
      100% { transform: translateX(100%); opacity: 0; }
    }
    
    @keyframes error-spark {
      0%, 100% { opacity: 0; transform: scale(0); }
      50% { opacity: 1; transform: scale(1); }
    }
    
    @keyframes bounce-broken {
      0%, 100% { transform: translateY(0px) rotateZ(0deg); }
      50% { transform: translateY(-15px) rotateZ(-5deg); }
    }
    
    @keyframes fade-in {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slide-down {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .float-animation {
      animation: float 4s ease-in-out infinite;
    }
    
    .pulse-glow {
      animation: pulse-glow 2s ease-in-out infinite;
    }
    
    .bridge-sway {
      animation: bridge-sway 3s ease-in-out infinite;
      transform-origin: center;
    }
    
    .cable-wave {
      animation: cable-wave 2s ease-in-out infinite;
      stroke-dasharray: 20;
    }
    
    .connection-pulse {
      animation: connection-pulse 1.5s ease-in-out infinite;
    }
    
    .connection-pulse-2 {
      animation: connection-pulse 1.5s ease-in-out infinite 0.3s;
    }
    
    .server-blink {
      animation: server-blink 2s ease-in-out infinite;
    }
    
    .server-blink-2 {
      animation: server-blink 2s ease-in-out infinite 0.5s;
    }
    
    .data-flow {
      animation: data-flow 2.5s ease-in infinite;
    }
    
    .data-flow-2 {
      animation: data-flow 2.5s ease-in infinite 0.8s;
    }
    
    .error-spark {
      animation: error-spark 1.5s ease-out infinite;
    }
    
    .error-spark-2 {
      animation: error-spark 1.5s ease-out infinite 0.3s;
    }
    
    .bounce-broken {
      animation: bounce-broken 2s ease-in-out infinite;
    }
    
    .fade-in {
      animation: fade-in 0.8s ease-out forwards;
    }
    
    .slide-down {
      animation: slide-down 0.8s ease-out forwards;
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
          <pattern id="grid-502" width="50" height="50" patternUnits="userSpaceOnUse">
            <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#f87171" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid-502)" />
      </svg>
    </div>
    
    <div class="relative z-10 text-center max-w-2xl mx-auto">
      
      <!-- Main illustration -->
      <div class="float-animation mb-8">
        <svg viewBox="0 0 400 320" class="w-full max-w-md mx-auto" xmlns="http://www.w3.org/2000/svg">
          <!-- Ground -->
          <ellipse cx="200" cy="290" rx="180" ry="20" fill="#ffffff" opacity="0.05"/>
          
          <!-- Bridge structure (gateway) -->
          <g class="bridge-sway" style="transform-origin: 200px 140px;">
            <!-- Left pillar -->
            <rect x="100" y="100" width="8" height="80" fill="none" stroke="#94a3b8" stroke-width="2" opacity="0.5"/>
            <!-- Right pillar -->
            <rect x="292" y="100" width="8" height="80" fill="none" stroke="#94a3b8" stroke-width="2" opacity="0.5"/>
            
            <!-- Bridge deck -->
            <path d="M 110 105 Q 200 85 296 105" fill="none" stroke="#ef4444" stroke-width="3" opacity="0.6"/>
            <path d="M 110 108 Q 200 90 296 108" fill="none" stroke="#ef4444" stroke-width="2" opacity="0.3"/>
            
            <!-- Support cables -->
            <line x1="120" y1="100" x2="200" y2="85" stroke="#fbbf24" stroke-width="1.5" opacity="0.4" class="cable-wave"/>
            <line x1="280" y1="100" x2="200" y2="85" stroke="#fbbf24" stroke-width="1.5" opacity="0.4" class="cable-wave" style="animation-delay: 0.5s;"/>
            
            <!-- Broken section -->
            <g class="bounce-broken" style="transform-origin: 200px 105px;">
              <rect x="190" y="95" width="20" height="25" fill="none" stroke="#ef4444" stroke-width="2"/>
              <line x1="190" y1="107" x2="210" y2="107" stroke="#ef4444" stroke-width="1" opacity="0.5"/>
            </g>
          </g>
          
          <!-- Server on left (working) -->
          <g style="transform: translate(80px, 200px);">
            <rect x="-12" y="-12" width="24" height="24" rx="3" fill="none" stroke="#94a3b8" stroke-width="2" class="connection-pulse"/>
            <circle cx="-3" cy="-3" r="2" fill="#94a3b8" class="server-blink"/>
            <circle cx="3" cy="-3" r="2" fill="#94a3b8" class="server-blink-2"/>
          </g>
          
          <!-- Server on right (broken) -->
          <g style="transform: translate(320px, 200px);">
            <rect x="-12" y="-12" width="24" height="24" rx="3" fill="none" stroke="#ef4444" stroke-width="2" class="connection-pulse-2"/>
            <circle cx="-3" cy="-3" r="2" fill="#ef4444" opacity="0.4"/>
            <circle cx="3" cy="-3" r="2" fill="#ef4444" opacity="0.4"/>
            <line x1="-8" y1="8" x2="8" y2="-8" stroke="#ef4444" stroke-width="1.5" opacity="0.6"/>
          </g>
          
          <!-- Connection attempt (data flowing) -->
          <g style="transform: translate(200px, 130px);">
            <!-- Data packet 1 -->
            <g class="data-flow">
              <circle cx="0" cy="0" r="3" fill="#fbbf24"/>
              <circle cx="0" cy="0" r="5" fill="none" stroke="#fbbf24" stroke-width="1" opacity="0.5"/>
            </g>
            <!-- Data packet 2 -->
            <g class="data-flow-2">
              <circle cx="0" cy="0" r="3" fill="#fbbf24"/>
              <circle cx="0" cy="0" r="5" fill="none" stroke="#fbbf24" stroke-width="1" opacity="0.5"/>
            </g>
          </g>
          
          <!-- Error sparks -->
          <g style="transform: translate(200px, 100px);">
            <circle cx="0" cy="0" r="2" fill="#ef4444" class="error-spark"/>
            <circle cx="15" cy="-10" r="2" fill="#ef4444" class="error-spark-2"/>
            <circle cx="-15" cy="-10" r="1.5" fill="#fbbf24" class="error-spark" style="animation-delay: 0.5s;"/>
          </g>
          
          <!-- Warning indicator -->
          <g style="transform: translate(200px, 260px);">
            <circle cx="0" cy="0" r="8" fill="none" stroke="#ef4444" stroke-width="1.5" opacity="0.5"/>
            <text x="0" y="3" font-family="'JetBrains Mono', monospace" font-size="14" font-weight="bold" fill="#ef4444" text-anchor="middle" class="connection-pulse">⚠</text>
          </g>
        </svg>
      </div>
      
      <!-- Error code -->
      <h1 id="error-title" class="text-8xl md:text-9xl font-bold mb-4 tracking-tight slide-down" style="font-family: 'JetBrains Mono', monospace; background: linear-gradient(135deg, #ef4444 0%, #fbbf24 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
        502
      </h1>
      
      <!-- Subtitle -->
      <h2 id="error-subtitle" class="text-2xl md:text-3xl font-semibold mb-6 fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #e2e8f0; animation-delay: 0.2s;">
        Bad Gateway
      </h2>
      
      <!-- Message -->
      <p id="error-message" class="text-lg mb-10 max-w-md mx-auto leading-relaxed fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #cbd5e1; animation-delay: 0.3s;">
        The server received an invalid response from an upstream server. This is usually temporary.
      </p>
      
      <!-- Action button -->
      <button id="retry-button" onclick="window.location.reload()" class="group relative px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 active:scale-95 fade-in" style="font-family: 'Space Grotesk', sans-serif; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; box-shadow: 0 10px 40px -10px rgba(239, 68, 68, 0.5); animation-delay: 0.4s;">
        <span class="relative z-10 flex items-center gap-3">
          <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Refresh Page
        </span>
      </button>
      
      <!-- Support info -->
      <div class="mt-10 flex flex-col gap-2" style="color: #64748b;">
        <div class="fade-in" style="animation-delay: 0.5s;">
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px;">🌉 Gateway communication failed</span>
        </div>
        <div class="fade-in" style="animation-delay: 0.6s;">
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px;">🔄 Please refresh or try again in a moment</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    const defaultConfig = {
      error_title: '502',
      error_subtitle: 'Bad Gateway',
      error_message: 'The server received an invalid response from an upstream server. This is usually temporary.',
      button_text: 'Refresh Page',
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
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9cfb7739e578807d',t:'MTc3MTM5NjEyMC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>