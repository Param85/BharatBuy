const initBackgroundAnimation = () => {
    if (document.getElementById('floating-bg-container')) return;
    
    const container = document.createElement('div');
    container.id = 'floating-bg-container';
    container.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        pointer-events: none;
        z-index: 2;
        opacity: 0.15; /* Clearly visible overlay backdrop */
        transition: opacity 0.5s ease;
    `;
    
    const style = document.createElement('style');
    style.innerHTML = `
        .ambient-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.9;
            will-change: transform;
            animation: floatBlob 25s infinite alternate ease-in-out;
        }
        .glow-emerald {
            width: 700px;
            height: 700px;
            top: -150px;
            left: -150px;
            background: radial-gradient(circle, rgba(6, 67, 59, 0.22) 0%, rgba(6, 67, 59, 0) 70%);
            animation-duration: 22s;
        }
        .glow-champagne {
            width: 800px;
            height: 800px;
            bottom: -200px;
            right: -150px;
            background: radial-gradient(circle, rgba(248, 231, 201, 0.25) 0%, rgba(248, 231, 201, 0) 70%);
            animation-duration: 30s;
            animation-delay: -7s;
        }
        .glow-gold {
            width: 600px;
            height: 600px;
            top: 35%;
            left: 45%;
            background: radial-gradient(circle, rgba(184, 142, 76, 0.16) 0%, rgba(184, 142, 76, 0) 70%);
            animation-duration: 38s;
            animation-delay: -12s;
        }

        @keyframes floatBlob {
            0% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(80px, 60px) scale(1.15);
            }
            66% {
                transform: translate(-50px, 90px) scale(0.9);
            }
            100% {
                transform: translate(0, 0) scale(1);
            }
        }
    `;
    document.head.appendChild(style);
    
    const blob1 = document.createElement('div');
    blob1.className = 'ambient-glow glow-emerald';
    
    const blob2 = document.createElement('div');
    blob2.className = 'ambient-glow glow-champagne';
    
    const blob3 = document.createElement('div');
    blob3.className = 'ambient-glow glow-gold';
    
    container.appendChild(blob1);
    container.appendChild(blob2);
    container.appendChild(blob3);
    
    document.body.appendChild(container);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBackgroundAnimation);
} else {
    initBackgroundAnimation();
}
