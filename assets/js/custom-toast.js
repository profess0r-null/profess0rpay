(function() {
    // Ensure container exists
    function getToastContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.position = 'fixed';
            container.style.top = '20px';
            container.style.right = '20px';
            container.style.zIndex = '9999';
            // We use absolute positioning inside for the stacking effect
            document.body.appendChild(container);
        }
        return container;
    }

    function updateToasts() {
        const container = document.getElementById('toast-container');
        if(!container) return;
        
        // Only get actual toasts
        const toasts = Array.from(container.querySelectorAll('.custom-toast'));
        toasts.forEach((toast, index) => {
            if(toast.dataset.isDragging === 'true') return;

            toast.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            if(index > 3) {
                toast.style.opacity = 0;
                toast.style.transform = `translateY(${index * 12}px) scale(0.8)`;
                toast.style.pointerEvents = 'none';
                setTimeout(() => { if(toast.parentNode) toast.remove(); }, 300);
            } else {
                const scale = 1 - (index * 0.05);
                const translateY = index * 12; // push down
                toast.style.transform = `translateY(${translateY}px) scale(${scale})`;
                toast.style.zIndex = 100 - index;
                toast.style.opacity = 1 - (index * 0.15);
            }
        });
    }

    window.createToast = function({ title = '', description = '', svg = '', timeout = 2500, type = 'success', top = 20 }) {
        const container = getToastContainer();
        container.style.top = top + 'px';

        const toast = document.createElement('div');
        toast.className = 'custom-toast';
        toast.setAttribute('role', 'alert');
        
        // Base styling for toast (Modern sleek design)
        toast.style.position = 'absolute';
        toast.style.top = '0';
        toast.style.right = '0';
        const isMobile = window.innerWidth <= 480;
        toast.style.width = isMobile ? 'calc(100vw - 40px)' : 'max-content';
        toast.style.minWidth = isMobile ? '0' : '280px';
        toast.style.maxWidth = isMobile ? 'calc(100vw - 40px)' : '360px';
        toast.style.padding = isMobile ? '12px 14px' : '14px 18px';
        toast.style.borderRadius = '12px';
        toast.style.boxShadow = '0 10px 40px rgba(0, 0, 0, 0.2), 0 2px 10px rgba(0, 0, 0, 0.1)';
        toast.style.display = 'flex';
        toast.style.alignItems = 'flex-start';
        toast.style.gap = isMobile ? '10px' : '14px';
        toast.style.cursor = 'grab';
        toast.style.backdropFilter = 'blur(12px)';
        toast.style.WebkitBackdropFilter = 'blur(12px)';
        
        // Initial state for entrance animation
        toast.style.transform = 'translateY(-20px) scale(0.95)';
        toast.style.opacity = '0';
        
        // Modern Dark Theme
        let bgColor = 'rgba(17, 24, 39, 0.95)'; // Gray-900 glass
        let borderColor = 'rgba(255, 255, 255, 0.08)';
        let textColor = '#F9FAFB'; // Gray-50
        let descColor = '#9CA3AF'; // Gray-400
        let iconColor = '#10B981'; // Emerald-500
        
        let titleLower = title ? title.toLowerCase() : '';
        let isError = type === 'error' || titleLower.includes('error') || titleLower.includes('fail') || titleLower.includes('wrong') || titleLower.includes('denied');

        if (isError) {
            iconColor = '#EF4444'; // Red-500
            bgColor = 'rgba(35, 15, 15, 0.95)'; // Dark red tinted glass
            borderColor = 'rgba(239, 68, 68, 0.15)'; // Red tinted border
        }

        let iconHtml = svg || (isError 
            ? `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`
        );

        toast.style.backgroundColor = bgColor;
        toast.style.border = `1px solid ${borderColor}`;
        toast.style.color = textColor;
        toast.style.fontFamily = "'Inter', 'Anek Bangla', sans-serif";

        let contentHtml = '';
        if (title && description) {
            contentHtml = `<div style="display: flex; flex-direction: column; gap: 4px; margin-top: 1px;">
                <span style="font-weight: 600; font-size: 14px; letter-spacing: -0.2px; color: ${textColor};">${title}</span>
                <span style="font-size: 13px; font-weight: 400; color: ${descColor}; line-height: 1.4;">${description}</span>
            </div>`;
        } else {
            contentHtml = `<span style="font-weight: 500; font-size: 14px; margin-top: 1px; letter-spacing: -0.1px; color: ${textColor};">${description || title}</span>`;
        }

        toast.innerHTML = `
            <div style="display: flex; align-items: flex-start; gap: 12px; width: 100%;">
                <span style="display: flex; align-items: center; justify-content: center; margin-top: 1px; flex-shrink: 0;">${iconHtml}</span>
                ${contentHtml}
            </div>
        `;

        // Swipe-to-dismiss logic
        let startX = 0;
        let isDragging = false;
        
        const onStart = (clientX) => {
            startX = clientX;
            isDragging = true;
            toast.dataset.isDragging = 'true';
            toast.style.transition = 'none';
            toast.style.cursor = 'grabbing';
        };

        const onMove = (clientX) => {
            if (!isDragging) return;
            const diffX = clientX - startX;
            const index = Array.from(container.querySelectorAll('.custom-toast')).indexOf(toast);
            const baseScale = 1 - (index * 0.05);
            const baseY = index * 12;
            
            if (diffX > 0) { 
                toast.style.transform = `translate(${diffX}px, ${baseY}px) scale(${baseScale})`;
                toast.style.opacity = Math.max(0, (1 - (index * 0.15)) - (diffX / 200));
            }
        };

        const removeToast = () => {
            toast.style.transition = 'all 0.3s ease';
            toast.style.transform = 'translateX(120%)';
            toast.style.opacity = 0;
            setTimeout(() => {
                if(toast.parentNode) toast.remove();
                updateToasts();
            }, 300);
        };

        const onEnd = (clientX) => {
            if (!isDragging) return;
            isDragging = false;
            toast.dataset.isDragging = 'false';
            toast.style.cursor = 'grab';
            
            const diffX = clientX - startX;
            if (diffX > 50) {
                removeToast();
            } else {
                updateToasts(); // Reset to calculated position
            }
        };

        toast.addEventListener('touchstart', (e) => onStart(e.touches[0].clientX), {passive: true});
        toast.addEventListener('touchmove', (e) => onMove(e.touches[0].clientX), {passive: true});
        toast.addEventListener('touchend', (e) => onEnd(e.changedTouches[0].clientX));

        toast.addEventListener('mousedown', (e) => onStart(e.clientX));
        toast.addEventListener('mousemove', (e) => onMove(e.clientX));
        toast.addEventListener('mouseup', (e) => onEnd(e.clientX));
        toast.addEventListener('mouseleave', (e) => { if (isDragging) onEnd(e.clientX); });

        // Add to DOM
        container.prepend(toast);
        
        // Trigger initial animation
        setTimeout(() => updateToasts(), 10);

        // Auto-remove after timeout
        let timeoutId = setTimeout(removeToast, timeout);
        
        // Pause timeout on hover
        toast.addEventListener('mouseenter', () => clearTimeout(timeoutId));
        toast.addEventListener('mouseleave', () => {
            if(!isDragging) timeoutId = setTimeout(removeToast, timeout / 2);
        });
    };
})();
