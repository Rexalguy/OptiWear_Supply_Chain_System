// SweetAlert2 handler for cart notifications
document.addEventListener('livewire:init', () => {
    Livewire.on('cart-updated', (event) => {
        // Load SweetAlert2 from CDN if not already loaded
        if (typeof window.Swal === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            script.onload = () => showNotification(event);
            script.onerror = () => {
                alert(event[0]?.title || event.title || 'Added to cart');
            };
            document.head.appendChild(script);
        } else {
            showNotification(event);
        }
    });
    
    function showNotification(event) {
        const notificationType = event[0]?.icon || event.icon || 'success';
        
        // Define colors based on notification type
        let backgroundColor, iconColor, progressBarColor;
        
        switch(notificationType) {
            case 'error':
            case 'danger':
                backgroundColor = '#fef2f2';
                iconColor = '#ef4444';
                progressBarColor = '#ef4444';
                break;
            case 'warning':
                backgroundColor = '#fffbeb';
                iconColor = '#f59e0b';
                progressBarColor = '#f59e0b';
                break;
            case 'success':
            default:
                backgroundColor = '#f0f9ff';
                iconColor = '#10b981';
                progressBarColor = '#10b981';
                break;
        }
        
        window.Swal.fire({
            text: event[0]?.title || event.title || 'Added to cart',
            icon: notificationType,
            timer: 3000,
            timerProgressBar: true,
            position: 'top-end',
            showConfirmButton: false,
            toast: true,
            width: '300px',
            iconColor: iconColor,
            background: backgroundColor,
            color: '#1f2937',
            showClass: {
                popup: ''
            },
            hideClass: {
                popup: ''
            },
            customClass: {
                popup: `small-toast toast-${notificationType}`
            }
        });
    }
});
