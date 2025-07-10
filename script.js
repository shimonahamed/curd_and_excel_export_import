let rowsPerPage = 15;  // ডিফল্ট
let currentPage = 1;
let users = [];

// "Rows per page" সিলেক্ট এলিমেন্ট থেকে ভ্যালু নিয়ে rowsPerPage আপডেট করে টেবিল রিফ্রেশ করার ফাংশন
document.getElementById('rowsPerPageSelect').addEventListener('change', function() {
  rowsPerPage = parseInt(this.value);
  currentPage = 1; 
  displayUsers(currentPage);
});


// বাকি কোড যেমন loadUsers, displayUsers, setupPagination আগের মতোই থাকবে
document.addEventListener('DOMContentLoaded', loadUsers);

document.getElementById('userForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const id = document.getElementById('user_id').value;
  const name = document.getElementById('name').value;
  const contactNumber = document.getElementById('contact_number').value;
  const email = document.getElementById('email').value;

  const formData = new FormData();
  formData.append('id', id);
  formData.append('name', name);
  formData.append('contact_number', contactNumber);
  formData.append('email', email);
  formData.append('action', id ? 'update' : 'create');

  fetch('api.php', {
    method: 'POST',
    body: formData
  }).then(res => res.json())
    .then(data => {
      alert(data.message);
      loadUsers(); // আবার ডাটা লোড করে টেবিল আপডেট করবে
    });
  
});
function loadUsers() {
  fetch('api.php?action=read')
    .then(res => res.json())
    .then(data => {
      users = data;
  
      displayUsers(currentPage);
    });
}

function displayUsers(page) {
  const userList = document.getElementById('userList');
  userList.innerHTML = '';

  let start = (page - 1) * rowsPerPage;
  let end = start + rowsPerPage;
  let paginatedUsers = users.slice(start, end);

  paginatedUsers.forEach((user, index) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="text-start">${start + index + 1}</td>
      <td class="text-start">${user.name}</td>
      <td>${user.contact_number || '-'}</td>
      <td>${user.email}</td>
      <td>
        <button onclick="editUser(${user.id}, '${user.name}','${user.contact_number}',  '${user.email}')" class="btn btn-sm btn-warning">Edit</button>
        <button onclick="deleteUser(${user.id})" class="btn btn-sm btn-danger">Delete</button>
      </td>
    `;
    userList.appendChild(tr);
  });

  setupPagination();
}

function editUser(id, name,contact_number, email) {
  document.getElementById('user_id').value = id;
  document.getElementById('name').value = name;
  document.getElementById('contact_number').value = contact_number; 
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
function setupPagination() {
  const pagination = document.getElementById('pagination');
  pagination.innerHTML = '';
  let pageCount = Math.ceil(users.length / rowsPerPage);
  function createPageButton(page, text = page) {
    const li = document.createElement('li');
    li.classList.add('page-item');
    if (page === currentPage) li.classList.add('active');

    li.innerHTML = `<a href="#" class="page-link">${text}</a>`;
    li.addEventListener('click', function (e) {
      e.preventDefault();
      currentPage = page;
      displayUsers(currentPage);
    });
    return li;
  }

  const prevLi = document.createElement('li');
  prevLi.classList.add('page-item');
  if (currentPage === 1) prevLi.classList.add('disabled');
  prevLi.innerHTML = `<a href="#" class="page-link">Previous</a>`;
  prevLi.addEventListener('click', function (e) {
    e.preventDefault();
    if (currentPage > 1) {
      currentPage--;
      displayUsers(currentPage);
    }
  });
  pagination.appendChild(prevLi);

  // Main pagination logic
  if (pageCount <= 10) {
    // Show all pages
    for (let i = 1; i <= pageCount; i++) {
      pagination.appendChild(createPageButton(i));
    }
  } else {
    // Show first 3
    for (let i = 1; i <= 3; i++) {
      pagination.appendChild(createPageButton(i));
    }

    // Dots if needed
    if (currentPage > 5) {
      const dot = document.createElement('li');
      dot.classList.add('page-item', 'disabled');
      dot.innerHTML = `<span class="page-link">...</span>`;
      pagination.appendChild(dot);
    }

    // Middle page (if current not near start or end)
    let startMiddle = Math.max(4, currentPage - 1);
    let endMiddle = Math.min(pageCount - 3, currentPage + 1);

    for (let i = startMiddle; i <= endMiddle; i++) {
      if (i > 3 && i < pageCount - 2) {
        pagination.appendChild(createPageButton(i));
      }
    }

    // Dots before last 3 pages
    if (currentPage < pageCount - 4) {
      const dot = document.createElement('li');
      dot.classList.add('page-item', 'disabled');
      dot.innerHTML = `<span class="page-link">...</span>`;
      pagination.appendChild(dot);
    }

    // Last 3 pages
    for (let i = pageCount - 2; i <= pageCount; i++) {
      pagination.appendChild(createPageButton(i));
    }
  }

  // Next button
  const nextLi = document.createElement('li');
  nextLi.classList.add('page-item');
  if (currentPage === pageCount) nextLi.classList.add('disabled');
  nextLi.innerHTML = `<a href="#" class="page-link">Next</a>`;
  nextLi.addEventListener('click', function (e) {
    e.preventDefault();
    if (currentPage < pageCount) {
      currentPage++;
      displayUsers(currentPage);
    }
  });
  pagination.appendChild(nextLi);
}



window.addEventListener('DOMContentLoaded', () => {
  // ডিফল্ট সিলেক্ট ভ্যালু rowsPerPage সেট করো
  const select = document.getElementById('rowsPerPageSelect');
  rowsPerPage = parseInt(select.value);

  loadUsers();

  const savedTheme = localStorage.getItem('theme') || 'light';
  toggleTheme(savedTheme);
});
