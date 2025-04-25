function updateCurrentTime() {
    const currentTimeElement = document.getElementById('current-time');
    if (currentTimeElement) {
        const now = new Date();
        currentTimeElement.textContent = `Current time: ${now.toLocaleDateString()} ${now.toLocaleTimeString()}`;
    }
}

function navigateToPostPage() {
    const form = document.getElementById('post-form-navigation');
    if (form) {
        form.action = 'post-page.html';
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    
    const postPageLink = document.getElementById('post-page-link');
    if (postPageLink) {
        postPageLink.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToPostPage();
        });
    }
}); 