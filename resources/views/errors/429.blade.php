<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>429 Too Many Requests</title>
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
    
    @keyframes gauge-fill {
      0%, 100% { stroke-dashoffset: 50; }
      50% { stroke-dashoffset: 10; }
    }
    
    @keyframes wave-pulse {
      0%, 100% { transform: translateX(0) scaleY(1); }
      50% { transform: translateX(-5px) scaleY(1.2); }
    }
    
    @keyframes wave-pulse-2 {
      0%, 100% { transform: translateX(0) scaleY(1); }
      50% { transform: translateX(5px) scaleY(1.2); }
    }
    
    @keyframes speedometer-spin {
      from { transform: rotate(-90deg); }
      to { transform: rotate(90deg); }
    }
    
    @keyframes speedometer-needle {
      0%, 100% { transform: rotate(0deg); }
      50% { transform: rotate(120deg); }
    }
    
    @keyframes pulse-warn {
      0%, 100% { opacity: 0.3; }
      50% { opacity: 1; }
    }
    
    @keyframes throttle-blink {
      0%, 100% { fill: #ef4444; opacity: 0.4; }
      50% { fill: #dc2626; opacity: 1; }
    }
    
    @keyframes bounce-request {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
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
    
    .gauge-fill {
      animation: gauge-fill 2.5s ease-in-out infinite;
    }
    
    .wave-pulse {
      animation: wave-pulse 1.5s ease-in-out infinite;
      transform-origin: center;
    }
    
    .wave-pulse-2 {
      animation: wave-pulse-2 1.5s ease-in-out infinite;
      transform-origin: center;
    }
    
    .speedometer-needle {
      animation: speedometer-needle 3s ease-in-out infinite;
      transform-origin: center;
    }
    
    .pulse-warn {
      animation: pulse-warn 1.5s ease-in-out infinite;
    }
    
    .pulse-warn-2 {
      animation: pulse-warn 1.5s ease-in-out infinite 0.3s;
    }
    
    .pulse-warn-3 {
      animation: pulse-warn 1.5s ease-in-out infinite 0.6s;
    }
    
    .throttle-blink {
      animation: throttle-blink 1s ease-in-out infinite;
    }
    
    .bounce-request {
      animation: bounce-request 2s ease-in-out infinite;
    }
    
    .bounce-request-2 {
      animation: bounce-request 2.2s ease-in-out infinite 0.2s;
    }
    
    .bounce-request-3 {
      animation: bounce-request 2.4s ease-in-out infinite 0.4s;
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
          <pattern id="grid-429" width="50" height="50" patternUnits="userSpaceOnUse">
            <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#f87171" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid-429)" />
      </svg>
    </div>
    
    <div class="relative z-10 text-center max-w-2xl mx-auto">
      
      <!-- Main illustration -->
      <div class="float-animation mb-8">
        <svg viewBox="0 0 400 320" class="w-full max-w-md mx-auto" xmlns="http://www.w3.org/2000/svg">
          <!-- Ground -->
          <ellipse cx="200" cy="290" rx="180" ry="20" fill="#ffffff" opacity="0.05"/>
          
          <!-- Speedometer gauge -->
          <g style="transform: translate(200px, 110px);">
            <!-- Gauge circle -->
            <circle cx="0" cy="0" r="50" fill="none" stroke="#94a3b8" stroke-width="2" opacity="0.3"/>
            <!-- Gauge arc (red zone) -->
            <path d="M -35 -35 A 50 50 0 0 1 35 -35" fill="none" stroke="#ef4444" stroke-width="4" opacity="0.5"/>
            <!-- Gauge arc (danger zone - animated) -->
            <path d="M 25 -42 A 50 50 0 0 1 35 -30" fill="none" stroke="#fbbf24" stroke-width="3" class="gauge-fill" stroke-dasharray="100" stroke-dashoffset="50"/>
            <!-- Needle -->
            <g class="speedometer-needle" style="transform-origin: 0 0;">
              <line x1="0" y1="0" x2="0" y2="-45" stroke="#ef4444" stroke-width="3" stroke-linecap="round"/>
            </g>
            <!-- Center point -->
            <circle cx="0" cy="0" r="6" fill="#ef4444"/>
          </g>
          
          <!-- Incoming request waves -->
          <g style="transform: translate(80px, 200px);">
            <g class="bounce-request">
              <circle cx="0" cy="0" r="8" fill="none" stroke="#fbbf24" stroke-width="2" class="pulse-warn"/>
              <text x="0" y="3" font-family="'JetBrains Mono', monospace" font-size="12" font-weight="bold" fill="#fbbf24" text-anchor="middle">→</text>
            </g>
          </g>
          
          <g style="transform: translate(200px, 200px);">
            <g class="bounce-request-2">
              <circle cx="0" cy="0" r="8" fill="none" stroke="#fbbf24" stroke-width="2" class="pulse-warn-2"/>
              <text x="0" y="3" font-family="'JetBrains Mono', monospace" font-size="12" font-weight="bold" fill="#fbbf24" text-anchor="middle">→</text>
            </g>
          </g>
          
          <g style="transform: translate(320px, 200px);">
            <g class="bounce-request-3">
              <circle cx="0" cy="0" r="8" fill="none" stroke="#fbbf24" stroke-width="2" class="pulse-warn-3"/>
              <text x="0" y="3" font-family="'JetBrains Mono', monospace" font-size="12" font-weight="bold" fill="#fbbf24" text-anchor="middle">→</text>
            </g>
          </g>
          
          <!-- Rate limiter indicator -->
          <g style="transform: translate(200px, 250px);">
            <rect x="-45" y="-8" width="90" height="16" rx="8" fill="none" stroke="#ef4444" stroke-width="2" opacity="0.4"/>
            <!-- Filled portion (rate limit) -->
            <rect x="-45" y="-8" width="75" height="16" rx="8" fill="#ef4444" opacity="0.3" class="throttle-blink"/>
            <!-- Warning text -->
            <text x="0" y="4" font-family="'JetBrains Mono', monospace" font-size="10" font-weight="bold" fill="#fbbf24" text-anchor="middle" opacity="0.7">LIMITED</text>
          </g>
          
          <!-- Wave motion lines showing traffic -->
          <g class="wave-pulse" style="transform-origin: 100px 150px;">
            <path d="M 70 150 Q 85 140 100 150 Q 115 160 130 150" fill="none" stroke="#fbbf24" stroke-width="1.5" opacity="0.4"/>
          </g>
          
          <g class="wave-pulse-2" style="transform-origin: 300px 150px;">
            <path d="M 270 150 Q 285 140 300 150 Q 315 160 330 150" fill="none" stroke="#fbbf24" stroke-width="1.5" opacity="0.4"/>
          </g>
        </svg>
      </div>
      
      <!-- Error code -->
      <h1 id="error-title" class="text-8xl md:text-9xl font-bold mb-4 tracking-tight slide-down" style="font-family: 'JetBrains Mono', monospace; background: linear-gradient(135deg, #ef4444 0%, #fbbf24 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
        429
      </h1>
      
      <!-- Subtitle -->
      <h2 id="error-subtitle" class="text-2xl md:text-3xl font-semibold mb-6 fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #e2e8f0; animation-delay: 0.2s;">
        Too Many Requests
      </h2>
      
      <!-- Message -->
      <p id="error-message" class="text-lg mb-10 max-w-md mx-auto leading-relaxed fade-in" style="font-family: 'Space Grotesk', sans-serif; color: #cbd5e1; animation-delay: 0.3s;">
        You are sending requests too quickly. Please slow down and try again after a moment.
      </p>
      
      <!-- Action button -->
      <button id="retry-button" onclick="window.location.reload()" class="group relative px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 active:scale-95 fade-in" style="font-family: 'Space Grotesk', sans-serif; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; box-shadow: 0 10px 40px -10px rgba(239, 68, 68, 0.5); animation-delay: 0.4s;">
        <span class="relative z-10 flex items-center gap-3">
          <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Wait & Retry
        </span>
      </button>
      
      <!-- Rate limit info -->
      <div class="mt-10 flex flex-col gap-2" style="color: #64748b;">
        <div class="fade-in" style="animation-delay: 0.5s;">
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px;">🚦 Rate limit exceeded - please wait before retrying</span>
        </div>
        <div class="fade-in" style="animation-delay: 0.6s;">
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px;">⏳ Limits reset in a few minutes</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    const defaultConfig = {
      error_title: '429',
      error_subtitle: 'Too Many Requests',
      error_message: 'You are sending requests too quickly. Please slow down and try again after a moment.',
      button_text: 'Wait & Retry',
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
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9cfb5338356d9a10',t:'MTc3MTM5NDY0Ni4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>