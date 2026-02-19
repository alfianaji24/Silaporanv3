<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 Internal Server Error</title>
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
    
    @keyframes spin-gear {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    @keyframes spin-gear-reverse {
      from { transform: rotate(360deg); }
      to { transform: rotate(0deg); }
    }
    
    @keyframes spark-flash {
      0%, 100% { opacity: 0; }
      50% { opacity: 1; }
    }
    
    @keyframes bounce-spark {
      0%, 100% { transform: translateY(0px) translateX(0px); }
      50% { transform: translateY(-20px) translateX(10px); }
    }
    
    @keyframes alert-pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
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
    
    .spin-gear {
      animation: spin-gear 4s linear infinite;
      transform-origin: center;
    }
    
    .spin-gear-reverse {
      animation: spin-gear-reverse 5s linear infinite;
      transform-origin: center;
    }
    
    .spark-flash {
      animation: spark-flash 1s ease-in-out infinite;
    }
    
    .spark-flash-2 {
      animation: spark-flash 1s ease-in-out infinite 0.3s;
    }
    
    .spark-flash-3 {
      animation: spark-flash 1s ease-in-out infinite 0.6s;
    }
    
    .bounce-spark {
      animation: bounce-spark 2s ease-in-out infinite;
    }
    
    .bounce-spark-2 {
      animation: bounce-spark 2.2s ease-in-out infinite 0.3s;
    }
    
    .alert-pulse {
      animation: alert-pulse 1.5s ease-in-out infinite;
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
          <pattern id="grid-500" width="50" height="50" patternUnits="userSpaceOnUse">
            <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#f87171" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid-500)" />
      </svg>
    </div>
    
    <div class="relative z-10 text-center max-w-2xl mx-auto">
      
      <!-- Main illustration -->
      <div class="float-animation mb-8">
        <svg viewBox="0 0 400 320" class="w-full max-w-md mx-auto" xmlns="http://www.w3.org/2000/svg">
          <!-- Ground -->
          <ellipse cx="200" cy="290" rx="180" ry="20" fill="#ffffff" opacity="0.05"/>
          
          <!-- Large gear (main) -->
          <g class="spin-gear" style="transform-origin: 200px 120px;">
            <!-- Gear circle -->
            <circle cx="200" cy="120" r="45" fill="none" stroke="#ef4444" stroke-width="3"/>
            <!-- Gear teeth -->
            <g stroke="#ef4444" stroke-width="2.5">
              <line x1="245" y1="120" x2="260" y2="120"/>
              <line x1="242" y1="102" x2="254" y2="90"/>
              <line x1="230" y1="90" x2="230" y2="75"/>
              <line x1="212" y1="80" x2="212" y2="65"/>
              <line x1="193" y1="78" x2="186" y2="63"/>
              <line x1="175" y1="90" x2="163" y2="78"/>
              <line x1="160" y1="105" x2="145" y2="90"/>
              <line x1="155" y1="120" x2="140" y2="120"/>
              <line x1="160" y1="135" x2="145" y2="150"/>
              <line x1="175" y1="150" x2="163" y2="162"/>
              <line x1="193" y1="162" x2="186" y2="177"/>
              <line x1="212" y1="160" x2="212" y2="175"/>
              <line x1="230" y1="150" x2="230" y2="165"/>
              <line x1="242" y1="138" x2="254" y2="150"/>
            </g>
            <!-- Center -->
            <circle cx="200" cy="120" r="12" fill="#ef4444"/>
            <circle cx="200" cy="120" r="6" fill="#0f172a"/>
          </g>
          
          <!-- Small gear (counter-rotating) -->
          <g class="spin-gear-reverse" style="transform-origin: 100px 200px;">
            <!-- Gear circle -->
            <circle cx="100" cy="200" r="30" fill="none" stroke="#fbbf24" stroke-width="2"/>
            <!-- Gear teeth -->
            <g stroke="#fbbf24" stroke-width="1.5">
              <line x1="130" y1="200" x2="140" y2="200"/>
              <line x1="128" y1="185" x2="135" y2="175"/>
              <line x1="115" y1="172" x2="115" y2="160"/>
              <line x1="100" y1="170" x2="100" y2="158"/>
              <line x1="85" y1="172" x2="85" y2="160"/>
              <line x1="72" y1="185" x2="65" y2="175"/>
              <line x1="70" y1="200" x2="60" y2="200"/>
              <line x1="72" y1="215" x2="65" y2="225"/>
              <line x1="85" y1="228" x2="85" y2="240"/>
              <line x1="100" y1="230" x2="100" y2="242"/>
              <line x1="115" y1="228" x2="115" y2="240"/>
              <line x1="128" y1="215" x2="135" y2="225"/>
            </g>
            <!-- Center -->
            <circle cx="100" cy="200" r="8" fill="#fbbf24"/>
            <circle cx="100" cy="200" r="4" fill="#0f172a"/>
          </g>
          
          <!-- Another small gear (counter-rotating) -->
          <g class="spin-gear" style="transform-origin: 300px 200px;">
            <!-- Gear circle -->
            <circle cx="300" cy="200" r="30" fill="none" stroke="#fbbf24" stroke-width="2"/>
            <!-- Gear teeth -->
            <g stroke="#fbbf24" stroke-width="1.5">
              <line x1="330" y1="200" x2="340" y2="200"/>
              <line x1="328" y1="185" x2="335" y2="175"/>
              <line x1="315" y1="172" x2="315" y2="160"/>
              <line x1="300" y1="170" x2="300" y2="158"/>
              <line x1="285" y1="172" x2="285" y2="160"/>
              <line x1="272" y1="185" x2="265" y2="175"/>
              <line x1="270" y1="200" x2="260" y2="200"/>
              <line x1="272" y1="215" x2="265" y2="225"/>
              <line x1="285" y1="228" x2="285" y2="240"/>
              <line x1="300" y1="230" x2="300" y2="242"/>
              <line x1="315" y1="228" x2="315" y2="240"/>
              <line x1="328" y1="215" x2="335" y2="225"/>
            </g>
            <!-- Center -->
            <circle cx="300" cy="200" r="8" fill="#fbbf24"/>
            <circle cx="300" cy="200" r="4" fill="#0f172a"/>
          </g>
          
          <!-- Spark effects -->
          <g class="bounce-spark" style="transform-origin: 150px 100px;">
            <circle cx="150" cy="100" r="3" fill="#fbbf24" class="spark-flash"/>
          </g>
          
          <g class="bounce-spark-2" style="transform-origin: 250px 100px;">
            <circle cx="250" cy="100" r="3" fill="#ef4444" class="spark-flash-2"/>
          </g>
          
          <!-- Alert indicator -->
          <g style="transform: translate(200px, 260px);">
            <circle cx="0" cy="0" r="18" fill="none" stroke="#ef4444" stroke-width="2" class="alert-pulse"/>
            <text x="0" y="6" font-family="'JetBrains Mono', monospace" font-size="20" font-weight="bold" fill="#ef4444" text-anchor="middle">!</text>
          </g>
          
          <!-- Error signal lines -->
          <line x1="80" y1="80" x2="100" y2="90" stroke="#ef4444" stroke-width="1" opacity="0.3" stroke-dasharray="3,3"/>
          <line x1="320" y1="80" x2="300" y2="90" stroke="#ef4444" stroke-width="1" opacity="0.3" stroke-dasharray="3,3"/>
        </svg>
      </div>
      
      <!-- Error code -->
      <h1 id="error-title" class="text-8xl md:text-9xl font-bold mb-4 tracking-tight slide-down" style="font-family: 'JetBrains Mono', monospace; background: linear-gradient(135deg, #ef4444 0%, #fbbf24 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
        500
      </h1>
      
      <!-- Subtitle -->
      <h2 id="error-subtitle" class="text-2xl md:text-3xl font-semibold mb-6 fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #e2e8f0; animation-delay: 0.2s;">
        Internal Server Error
      </h2>
      
      <!-- Message -->
      <p id="error-message" class="text-lg mb-10 max-w-md mx-auto leading-relaxed fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #cbd5e1; animation-delay: 0.3s;">
        Something went wrong on our end. Our team has been notified and is working to fix it.
      </p>
      
      <!-- Action button -->
      <button id="retry-button" onclick="window.location.reload()" class="group relative px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 active:scale-95 fade-in" style="font-family: 'Space Grotesk', sans-serif; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; box-shadow: 0 10px 40px -10px rgba(239, 68, 68, 0.5); animation-delay: 0.4s;">
        <span class="relative z-10 flex items-center gap-3">
          <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Try Again
        </span>
      </button>
      
      <!-- Support info -->
      <div class="mt-10 flex flex-col gap-2" style="color: #64748b;">
        <div class="fade-in" style="animation-delay: 0.5s;">
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px;">🔧 Error ID: 500-ERR</span>
        </div>
        <div class="fade-in" style="animation-delay: 0.6s;">
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px;">📞 Contact support if the issue persists</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    const defaultConfig = {
      error_title: '500',
      error_subtitle: 'Internal Server Error',
      error_message: "Something went wrong on our end. Our team has been notified and is working to fix it.",
      button_text: 'Try Again',
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
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9cfb4dd107da9a10',t:'MTc3MTM5NDQyNC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>