// main.js

// Language Switcher
document.addEventListener('DOMContentLoaded', function() {
    const langButtons = document.querySelectorAll('.language-switch');
    langButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const lang = this.dataset.lang;
            fetch('/wellbeing-directory/ajax/language.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'lang=' + encodeURIComponent(lang)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload to update language
                }
            });
        });
    });
});

// AJAX Booking Form Submission (if present)
const bookingForm = document.getElementById('booking-form');
if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(bookingForm);
        fetch('/wellbeing-directory/ajax/booking.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const msg = document.getElementById('booking-message');
            if (msg) {
                msg.textContent = data.message;
                msg.className = data.success ? 'alert alert-success' : 'alert alert-danger';
            }
            if (data.success) bookingForm.reset();
        });
    });
}

// Feedback AJAX Form Submission (if present)
const feedbackForm = document.getElementById('feedback-form');
if (feedbackForm) {
    feedbackForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(feedbackForm);
        fetch('/wellbeing-directory/ajax/feedback.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const msg = document.getElementById('feedback-message');
            if (msg) {
                msg.textContent = data.message;
                msg.className = data.success ? 'alert alert-success' : 'alert alert-danger';
            }
            if (data.success) feedbackForm.reset();
        });
    });
}

// Messaging (Send and Fetch)
function fetchMessages(withId) {
    fetch('/wellbeing-directory/ajax/messaging.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=fetch&with_id=' + encodeURIComponent(withId)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const list = document.getElementById('message-list');
            if (list) {
                list.innerHTML = '';
                data.messages.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg.message + ' (' + msg.sent_at + ')';
                    list.appendChild(li);
                });
            }
        }
    });
}

const messageForm = document.getElementById('message-form');
if (messageForm) {
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const withId = messageForm.dataset.withId;
        const formData = new FormData(messageForm);
        formData.append('action', 'send');
        fetch('/wellbeing-directory/ajax/messaging.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const msg = document.getElementById('messaging-message');
            if (msg) {
                msg.textContent = data.message;
                msg.className = data.success ? 'alert alert-success' : 'alert alert-danger';
            }
            if (data.success && withId) {
                fetchMessages(withId);
                messageForm.reset();
            }
        });
    });

    // Optionally fetch messages every 5 seconds
    const withId = messageForm.dataset.withId;
    if (withId) {
        setInterval(function() {
            fetchMessages(withId);
        }, 5000);
    }
}

// Service Directory Live Search (if present)
const serviceSearchInput = document.getElementById('service-search');
if (serviceSearchInput) {
    serviceSearchInput.addEventListener('input', function() {
        const q = serviceSearchInput.value.trim();
        const language = document.documentElement.lang || 'en';
        if (q.length < 2) return;
        fetch('/wellbeing-directory/ajax/service-search.php?q=' + encodeURIComponent(q) + '&language=' + encodeURIComponent(language))
        .then(res => res.json())
        .then(data => {
            const results = document.getElementById('service-search-results');
            if (results) {
                results.innerHTML = '';
                if (data.success && data.results.length) {
                    data.results.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'search-result-item';
                        div.textContent = item.name + (item.location ? ' - ' + item.location : '');
                        results.appendChild(div);
                    });
                } else {
                    results.innerHTML = '<div>No results found.</div>';
                }
            }
        });
    });
}