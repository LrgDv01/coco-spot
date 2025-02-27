import './bootstrap';

import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// import Echo from 'laravel-echo';
// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

// document.addEventListener('DOMContentLoaded', function() {
//     // Initial notification count
//     updateNotificationCount();

//     // Real-time listener
//     window.Echo.channel('admin-notifications')
//         .listen('NewFarmerReport', (e) => {
//             addNewNotification(e.report);
//             updateNotificationCount();
//             showToast(e.report); // Optional: show a toast notification
//         });

//     // Fetch initial notifications
//     fetchNotifications();

//     // Mark as read when clicked
//     document.querySelectorAll('.notification-item').forEach(item => {
//         item.addEventListener('click', function() {
//             markAsRead(this.dataset.id);
//         });
//     });
// });

// function fetchNotifications() {
//     fetch('/admin/notifications')
//         .then(response => response.json())
//         .then(data => {
//             const notificationList = document.getElementById('notification-list');
//             notificationList.innerHTML = '';
//             data.forEach(notification => addNewNotification(notification));
//         });
// }

// function addNewNotification(notification) {
//     const notificationList = document.getElementById('notification-list');
//     const html = `
//         <a href="#" class="list-group-item list-group-item-action flex-column align-items-start notification-item" data-id="${notification.id}">
//             <div class="d-flex w-100 justify-content-between">
//                 <h5 class="mb-1">${notification.farmer_name}</h5>
//                 <small class="text-muted">${new Date(notification.created_at).toLocaleString()}</small>
//             </div>
//             <p class="mb-1">${notification.content}</p>
//             ${notification.status === 'unread' ? '<span class="badge bg-primary">New</span>' : ''}
//         </a>
//     `;
//     notificationList.insertAdjacentHTML('afterbegin', html);
// }

// function updateNotificationCount() {
//     fetch('/admin/notifications')
//         .then(response => response.json())
//         .then(data => {
//             const unreadCount = data.filter(n => n.status === 'unread').length;
//             const countElement = document.getElementById('notification-count');
//             countElement.style.display = unreadCount > 0 ? 'block' : 'none';
//             countElement.innerHTML = unreadCount > 0 ? `<span>${unreadCount}</span>` : '';
//         });
// }

// function markAsRead(id) {
//     fetch(`/admin/notifications/${id}/read`, {
//         method: 'POST',
//         headers: {
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         }
//     })
//     .then(() => {
//         updateNotificationCount();
//         fetchNotifications();
//     });
// }

// function showToast(report) {
//     // Implement your preferred toast notification here
//     console.log(`New report from ${report.farmer_name}: ${report.content}`);
// }