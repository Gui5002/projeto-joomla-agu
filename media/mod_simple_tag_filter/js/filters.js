window.addEventListener("load", function() {
    const container = document.querySelector('.simple-tag-filter--tabs');
    if (!container) return;
    const glider = container.querySelector('.simple-tag-filter__glider');
    if (!glider) return;
    const buttons = container.querySelectorAll('.btn');
    if (!buttons) return;
    const selectedButtonClass = 'btn-info';

    const moveGlider = (el) => {
        glider.style.display = 'block';
        glider.style.width = el.offsetWidth + 'px';
        glider.style.height = el.offsetHeight + 'px';
        glider.style.left = el.offsetLeft + 'px';
        glider.style.top = el.offsetTop + 'px';
    };

    const clearGlider = () => {
        glider.style.display = 'none';
        glider.style.width = 0;
        glider.style.height = 0;
        glider.style.top = '100%';
    }

    // Set initial position
    let activeBtn = container.querySelector('.btn.active');
    if (activeBtn) {
        moveGlider(activeBtn);
        // Add this to scroll the active button into view
        activeBtn.scrollIntoView({
            behavior: 'smooth', // Optional: smooth scrolling
            block: 'nearest',   // Prevents vertical jumping of the page
            inline: 'center'    // Centers the element horizontally in the container
        });
    }

    // Move on hover
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            moveGlider(btn);
            activateButton(btn);
        });

        btn.addEventListener('click', () => {
            moveGlider(btn);
            activateButton(btn);
            activeBtn = btn;
        });
    });

    // Return to active on mouse leave
    container.addEventListener('mouseleave', () => {
        if (activeBtn) {
            moveGlider(activeBtn);
            activateButton(activeBtn)
        } else {
            clearActiveButtons();
            clearGlider()
        }
    });

    let activateButton = (el) => {
        clearActiveButtons();
        el.classList.add('active');
        el.classList.add(selectedButtonClass);
    }

    let clearActiveButtons = () => {
        buttons.forEach(btn => {
            btn.classList.remove('active');
            btn.classList.remove(selectedButtonClass);
        })
    }
});