<?php
include "db.php";
include "auth.php";

if ($_SESSION['role'] != 'admin') {
    die("Access Denied");
}

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin – User Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
    --primary:#667eea;
    --secondary:#764ba2;
    --glass:rgba(255,255,255,.88);
    --text:#222;
    --muted:#777;
}
body.dark{
    --glass:rgba(28,28,45,.96);
    --text:#f1f1f1;
    --muted:#aaa;
}

*{box-sizing:border-box}

body{
    margin:0;
    font-family:"Segoe UI",sans-serif;
    background:linear-gradient(-45deg,#667eea,#f687b3,#63cdda,#fcd34d);
    background-size:400% 400%;
    animation:bg 15s ease infinite;
    color:var(--text);
}
@keyframes bg{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* NAVBAR */
.navbar{
    padding:18px 28px;
    background:rgba(0,0,0,.28);
    backdrop-filter:blur(12px);
    color:#fff;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.navbar a{
    color:#fff;
    text-decoration:none;
    margin-left:20px;
    font-size:18px;
    transition:.25s;
}
.navbar a:hover{opacity:.8}

/* CONTAINER */
.container{
    padding:28px;
    max-width:1250px;
    margin:auto;
}

/* CARD */
.card{
    background:var(--glass);
    backdrop-filter:blur(14px);
    border-radius:24px;
    padding:28px;
    box-shadow:0 30px 60px rgba(0,0,0,.25);
    animation:fadeUp .6s ease;
}
@keyframes fadeUp{
    from{opacity:0;transform:translateY(24px)}
    to{opacity:1;transform:none}
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:14px;
}
.header h2{
    margin:0;
    font-size:26px;
    letter-spacing:.3px;
}

/* BUTTONS */
.btn{
    padding:12px 20px;
    border-radius:30px;
    border:none;
    cursor:pointer;
    font-weight:600;
    transition:.3s;
    position:relative;
    overflow:hidden;
}
.primary{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;
}
.secondary{
    background:#eee;
}
.btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(0,0,0,.25);
}

/* FLASH */
.flash{
    background:#d4edda;
    color:#155724;
    padding:16px;
    border-radius:14px;
    margin:20px 0;
    font-weight:600;
}

/* CONTROLS */
.controls{
    display:flex;
    gap:14px;
    flex-wrap:wrap;
    margin:22px 0;
}
.controls input,
.controls select{
    padding:12px 16px;
    border-radius:30px;
    border:1px solid #ccc;
    outline:none;
    font-size:14px;
    transition:.3s;
}
.controls input:focus,
.controls select:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 4px rgba(102,126,234,.25);
}

/* TABLE */
.table-box{
    overflow-x:auto;
}
table{
    width:100%;
    border-collapse:collapse;
}
thead th{
    position:sticky;
    top:0;
    z-index:2;
    background:rgba(0,0,0,.07);
    backdrop-filter:blur(6px);
}
th,td{
    padding:16px;
}
tr{
    transition:.25s;
}
tbody tr:hover{
    background:rgba(102,126,234,.12);
    box-shadow:inset 0 0 0 999px rgba(102,126,234,.08);
}

/* ROLE SELECT */
.roleSelect{
    padding:8px 14px;
    border-radius:20px;
    border:1px solid #ccc;
    cursor:pointer;
    background:#fff;
}

/* ACTIONS */
.action a{
    margin-right:16px;
    font-size:18px;
    transition:.25s;
}
.edit{color:var(--primary)}
.delete{color:#dc3545}
.action a:hover{
    transform:scale(1.15);
}

/* FOOTER */
.footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:22px;
    flex-wrap:wrap;
    color:var(--muted);
}

/* PAGINATION */
#pagination button{
    padding:8px 14px;
    margin:4px;
    border:none;
    border-radius:20px;
    background:var(--primary);
    color:#fff;
    cursor:pointer;
    transition:.3s;
}
#pagination button:hover{opacity:.85}

/* MOBILE */
@media(max-width:768px){
    table,thead,tbody,tr,th,td{display:block}
    thead{display:none}
    tr{
        background:var(--glass);
        margin-bottom:18px;
        border-radius:20px;
        padding:18px;
        box-shadow:0 12px 28px rgba(0,0,0,.18);
    }
    td{
        padding:8px 0;
    }
    td::before{
        content:attr(data-label);
        font-weight:600;
        opacity:.65;
        display:block;
    }
}
</style>
</head>

<body>

<div class="navbar">
    <strong><i class="fa-solid fa-users"></i> Admin Panel</strong>
    <div>
        <a href="dashboard.php"><i class="fa fa-home"></i></a>
        <a href="#" onclick="toggleDark()">🌙</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i></a>
    </div>
</div>

<div class="container">
<div class="card">

<div class="header">
    <h2>User Management</h2>
    <div>
        <a href="add.php" class="btn primary">
            <i class="fa fa-user-plus"></i> Add
        </a>
        <button class="btn secondary" onclick="exportCSV()">
            <i class="fa fa-file-csv"></i> Export
        </button>
    </div>
</div>

<?php if($msg==='deleted'): ?>
<div class="flash">User deleted successfully</div>
<?php endif; ?>

<div class="controls">
    <input type="text" id="search" placeholder="🔍 Search users">
    <select id="roleFilter">
        <option value="">All Roles</option>
        <option value="admin">Admin</option>
        <option value="user">User</option>
    </select>
    <select id="rowsLimit">
        <option value="5">5 rows</option>
        <option value="10">10 rows</option>
        <option value="50">50 rows</option>
    </select>
</div>

<div class="table-box">
<table id="userTable">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php
$result = mysqli_query($conn,"SELECT * FROM users");
while($row=mysqli_fetch_assoc($result)){
?>
<tr data-role="<?= $row['role'] ?>">
<td data-label="ID"><?= $row['id'] ?></td>
<td data-label="Name"><?= htmlspecialchars($row['name']) ?></td>
<td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
<td data-label="Role">
<select class="roleSelect">
<option <?= $row['role']=='admin'?'selected':'' ?>>admin</option>
<option <?= $row['role']=='user'?'selected':'' ?>>user</option>
</select>
</td>
<td data-label="Action" class="action">
<a class="edit" href="edit.php?id=<?= $row['id'] ?>"><i class="fa fa-edit"></i></a>
<a class="delete" href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete user?')">
<i class="fa fa-trash"></i>
</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>

<div class="footer">
    <div id="count"></div>
    <div id="pagination"></div>
</div>

</div>
</div>

<script>
function toggleDark(){document.body.classList.toggle("dark")}

function exportCSV(){
    let rows=[...document.querySelectorAll("table tr")];
    let csv=rows.map(r=>[...r.children].map(td=>td.innerText).join(",")).join("\n");
    let blob=new Blob([csv],{type:"text/csv"});
    let a=document.createElement("a");
    a.href=URL.createObjectURL(blob);
    a.download="users.csv";
    a.click();
}

const search=document.getElementById("search");
const roleFilter=document.getElementById("roleFilter");
const rowsLimit=document.getElementById("rowsLimit");
const rows=[...document.querySelectorAll("#userTable tbody tr")];
const pagination=document.getElementById("pagination");
const countBox=document.getElementById("count");
let page=1;

function render(){
    let filtered=rows.filter(r=>{
        return r.innerText.toLowerCase().includes(search.value.toLowerCase()) &&
        (!roleFilter.value || r.dataset.role===roleFilter.value);
    });
    let limit=parseInt(rowsLimit.value);
    let pages=Math.ceil(filtered.length/limit);
    let start=(page-1)*limit;

    rows.forEach(r=>r.style.display="none");
    filtered.slice(start,start+limit).forEach(r=>r.style.display="");

    pagination.innerHTML="";
    for(let i=1;i<=pages;i++){
        let b=document.createElement("button");
        b.textContent=i;
        if(i===page)b.style.opacity=".6";
        b.onclick=()=>{page=i;render()};
        pagination.appendChild(b);
    }
    countBox.textContent=`Showing ${filtered.length} users`;
}
[search,roleFilter,rowsLimit].forEach(e=>e.addEventListener("input",()=>{page=1;render()}));
render();
</script>

</body>
</html>
