document.getElementById('action-clear-all-mails').addEventListener('click', function(event) {
    if (!confirm('Do you really want to clear the mbox and remove all mails within?')) {
        event.preventDefault();
    }
});
