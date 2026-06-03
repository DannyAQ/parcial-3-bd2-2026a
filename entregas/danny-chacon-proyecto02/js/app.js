/* =========================================
   FARMACIA PREMIUM - APP.JS
   Estilo moderno tipo Starlink
========================================= */

/* =========================================
   SIDEBAR ACTIVE
========================================= */

const menuLinks = document.querySelectorAll('.sidebar-menu a');

menuLinks.forEach(link => {

    link.addEventListener('click', () => {

        menuLinks.forEach(item => {
            item.classList.remove('active');
        });

        link.classList.add('active');

    });

});

/* =========================================
   CARD HOVER EFFECT
========================================= */

const cards = document.querySelectorAll(
    '.card, .product-card'
);

cards.forEach(card => {

    card.addEventListener('mouseenter', () => {

        card.style.transform = 'translateY(-6px)';
        card.style.boxShadow =
            '0 0 30px rgba(0,210,106,0.2)';

    });

    card.addEventListener('mouseleave', () => {

        card.style.transform = 'translateY(0px)';
        card.style.boxShadow = 'none';

    });

});

/* =========================================
   BUTTON RIPPLE EFFECT
========================================= */

const buttons = document.querySelectorAll('.btn');

buttons.forEach(button => {

    button.addEventListener('click', function (e) {

        const ripple = document.createElement('span');

        ripple.classList.add('ripple');

        const rect = button.getBoundingClientRect();

        ripple.style.left =
            e.clientX - rect.left + 'px';

        ripple.style.top =
            e.clientY - rect.top + 'px';

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);

    });

});

/* =========================================
   FADE IN ANIMATION
========================================= */

const observer = new IntersectionObserver(entries => {

    entries.forEach(entry => {

        if (entry.isIntersecting) {

            entry.target.classList.add('show');

        }

    });

}, {
    threshold: 0.1
});

document.querySelectorAll(
    '.card, .product-card, .table-container'
).forEach(el => {

    el.classList.add('hidden');

    observer.observe(el);

});

/* =========================================
   SEARCH PRODUCT
========================================= */

const searchInput = document.querySelector('#searchProduct');

if (searchInput) {

    searchInput.addEventListener('keyup', () => {

        const value =
            searchInput.value.toLowerCase();

        const products =
            document.querySelectorAll('.product-card');

        products.forEach(product => {

            const text =
                product.innerText.toLowerCase();

            if (text.includes(value)) {

                product.style.display = 'block';

            } else {

                product.style.display = 'none';

            }

        });

    });

}

/* =========================================
   DARK MODE TOGGLE
========================================= */

const themeBtn =
    document.querySelector('#themeToggle');

if (themeBtn) {

    themeBtn.addEventListener('click', () => {

        document.body.classList.toggle('light-mode');

        localStorage.setItem(
            'theme',
            document.body.classList.contains('light-mode')
                ? 'light'
                : 'dark'
        );

    });

}

/* =========================================
   LOAD THEME
========================================= */

window.addEventListener('load', () => {

    const theme =
        localStorage.getItem('theme');

    if (theme === 'light') {

        document.body.classList.add('light-mode');

    }

});

/* =========================================
   COUNTER ANIMATION
========================================= */

const counters =
    document.querySelectorAll('.card-value');

counters.forEach(counter => {

    const updateCounter = () => {

        const target =
            +counter.innerText.replace(/\D/g, '');

        let current =
            +counter.getAttribute('data-count') || 0;

        const increment = target / 40;

        if (current < target) {

            current += increment;

            counter.setAttribute(
                'data-count',
                current
            );

            counter.innerText =
                '$' + Math.floor(current).toLocaleString();

            requestAnimationFrame(updateCounter);

        } else {

            counter.innerText =
                '$' + target.toLocaleString();

        }

    };

    updateCounter();

});

/* =========================================
   LOADING SCREEN
========================================= */

window.addEventListener('load', () => {

    const loader =
        document.querySelector('.loader');

    if (loader) {

        loader.style.opacity = '0';

        setTimeout(() => {

            loader.style.display = 'none';

        }, 500);

    }

});

/* =========================================
   NOTIFICATION SYSTEM
========================================= */

function showNotification(message, type = 'success') {

    const notification =
        document.createElement('div');

    notification.className =
        `notification ${type}`;

    notification.innerHTML = `
        <span>${message}</span>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {

        notification.classList.add('show');

    }, 100);

    setTimeout(() => {

        notification.classList.remove('show');

        setTimeout(() => {

            notification.remove();

        }, 300);

    }, 3000);

}

/* =========================================
   EXAMPLE BUTTON ACTION
========================================= */

const buyButtons =
    document.querySelectorAll('.btn-primary');

buyButtons.forEach(button => {

    button.addEventListener('click', () => {

        showNotification(
            'Producto agregado correctamente'
        );

    });

});

/* =========================================
   TABLE ROW HOVER
========================================= */

const tableRows =
    document.querySelectorAll('tbody tr');

tableRows.forEach(row => {

    row.addEventListener('mouseenter', () => {

        row.style.transform = 'scale(1.01)';

    });

    row.addEventListener('mouseleave', () => {

        row.style.transform = 'scale(1)';

    });

});

/* =========================================
   FLOATING PARTICLES
========================================= */

function createParticle() {

    const particle =
        document.createElement('div');

    particle.classList.add('particle');

    particle.style.left =
        Math.random() * window.innerWidth + 'px';

    particle.style.animationDuration =
        Math.random() * 3 + 2 + 's';

    particle.style.opacity =
        Math.random();

    document.body.appendChild(particle);

    setTimeout(() => {

        particle.remove();

    }, 5000);

}

setInterval(createParticle, 800);

/* =========================================
   MOBILE MENU
========================================= */

const mobileBtn =
    document.querySelector('#mobileMenu');

const sidebar =
    document.querySelector('.sidebar');

if (mobileBtn) {

    mobileBtn.addEventListener('click', () => {

        sidebar.classList.toggle('show-sidebar');

    });

}

/* =========================================
   SMOOTH SCROLL
========================================= */

document.querySelectorAll('a[href^="#"]')
    .forEach(anchor => {

        anchor.addEventListener('click', function (e) {

            e.preventDefault();

            const target =
                document.querySelector(
                    this.getAttribute('href')
                );

            if (target) {

                target.scrollIntoView({
                    behavior: 'smooth'
                });

            }

        });

    });

/* =========================================
   CLOCK
========================================= */

function updateClock() {

    const clock =
        document.querySelector('#clock');

    if (clock) {

        const now = new Date();

        clock.innerHTML =
            now.toLocaleTimeString();

    }

}

setInterval(updateClock, 1000);

/* =========================================
   CONSOLE BRANDING
========================================= */

console.log(`
=========================================
 FARMACIA PREMIUM SYSTEM
 Diseño inspirado en Starlink
=========================================
`);