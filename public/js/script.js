
    document.addEventListener("DOMContentLoaded", function () {
        const currentPath = window.location.pathname;
        const menuItems = document.querySelectorAll('.menu-item');

        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href === currentPath || href === window.location.href) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    });
