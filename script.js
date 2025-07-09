document.addEventListener('DOMContentLoaded', loadUsers);

document.getElementById('userForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const id = document.getElementById('user_id').value;
  const name = document.getElementById('name').value;
  const email = document.getElementById('email').value;

  const formData = new FormData();
  formData.append('id', id);
  formData.append('name', name);
  formData.append('email', email);
  formData.append('action', id ? 'update' : 'create');

  fetch('api.php', {
    method: 'POST',
    body: formData
  }).then(res => res.json())
    .then(data => {
      alert(data.message);
      loadUsers();
      document.getElementById('userForm').reset();
    });
});

function loadUsers() {
  fetch('api.php?action=read')
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector('#userTable tbody');
      tbody.innerHTML = '';
      data.forEach((user,index) => {
        tbody.innerHTML += `
          <tr>
            <td>${index +1}</td>
            <td>${user.name}</td>
            <td>${user.contact_number}</td>
            <td>${user.email}</td>
            <td>
              <button onclick="editUser(${user.id}, '${user.name}', '${user.email}')" class="table-hedar">Edit</button>
              <button onclick="deleteUser(${user.id})">Delete</button>
            </td>
          </tr>`;
      });
    });
}

function editUser(id, name, email) {
  document.getElementById('user_id').value = id;
  document.getElementById('name').value = name;
  document.getElementById('email').value = email;
}

function deleteUser(id) {
  if (confirm("Are you sure?")) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', 'delete');

    fetch('api.php', {
      method: 'POST',
      body: formData
    }).then(res => res.json())
      .then(data => {
        alert(data.message);
        loadUsers();
      });
  }
}

function toggleTheme(theme) {

  // Store theme in localStorage
  localStorage.setItem('theme', theme);

  // Set attribute for data-theme (can be used in CSS)
  document.documentElement.setAttribute('data-theme', theme);

  // Remove old theme class if any
  document.body.classList.remove('light', 'dark');

  // Add new theme class
  document.body.classList.add(theme);
}

window.addEventListener('DOMContentLoaded', () => {
  const savedTheme = localStorage.getItem('theme') || 'light';
  toggleTheme(savedTheme);
});







// Make sure users variable is global
// let users = JSON.parse(localStorage.getItem('users')) || [];





