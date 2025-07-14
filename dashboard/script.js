document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('aside a');

    links.forEach(link => {
        link.addEventListener('click', () => {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) spinner.style.display = 'flex';
        });
    });

    if (links.length > 0) {
        links[0].click();
    }
});
