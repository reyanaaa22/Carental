<style>
.ts-sidebar {
	background-color: #004153;
	width: 250px;
	min-height: 100vh;
	color: #fff;
	position: fixed;
	top: 0;
	left: 0;
	z-index: 999;
	padding-top: 80px; /* space for header */
	box-sizing: border-box;
}

.ts-sidebar-menu {
	list-style: none;
	padding: 0;
	margin: 0;
}

.ts-sidebar-menu li {
	border-bottom: 1px solid rgba(255, 255, 255, 0.1);
	position: relative;
}

.ts-sidebar-menu li a {
	display: flex;
	align-items: center;
	color: #fff;
	text-decoration: none;
	padding: 8px 15px;
	transition: background 0.3s;
	cursor: pointer;
	justify-content: flex-start;
	gap: 10px;
}

.ts-sidebar-menu li a:hover {
	background-color: #006080;
}

.ts-sidebar-menu .ts-label {
	padding: 15px 20px;
	font-weight: bold;
	text-transform: uppercase;
	font-size: 14px;
	color: #cce7ff;
}

.ts-sidebar-menu ul {
	list-style: none;
	padding-left: 0;
	max-height: 0;
	overflow: hidden;
	background-color: #00526d;
	transition: max-height 0.3s ease;
}

.ts-sidebar-menu ul.show {
	max-height: 500px; /* large enough for inner items */
}

.ts-sidebar-menu ul li a {
	font-size: 14px;
	padding: 10px 30px;
}

.dropdown-icon {
	margin-left: auto;
	transition: transform 0.3s;
}

.dropdown-open .dropdown-icon {
	transform: rotate(90deg);
}
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<nav class="ts-sidebar">
	<ul class="ts-sidebar-menu">
		<li style="text-align: center; padding: 20px 20px 10px 20px;">
<img src="images/logo.png" alt="Logo" style="max-width: 150px; height: auto; margin-top: -40px;">
		</li>

		<li><a href="../admin/dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>

<li class="has-dropdown">
	<a><i class="fa fa-tags"></i> Brands <span class="dropdown-icon">&#9660;</span></a>
	<ul>
		<li><a href="../admin/create_brand.php"><i class="fa fa-plus"></i> Create Brand</a></li>
		<li><a href="../admin/manage_brand.php"><i class="fa fa-cogs"></i> Manage Brands</a></li>
	</ul>
</li>

<li class="has-dropdown">
	<a><i class="fa fa-car"></i> Vehicles <span class="dropdown-icon">&#9660;</span></a>
	<ul>
		<li><a href="../admin/post_vehicles.php"><i class="fa fa-upload"></i> Post Vehicle</a></li>
		<li><a href="../admin/manage_vehicles.php"><i class="fa fa-wrench"></i> Manage Vehicles</a></li>
	</ul>
</li>

<li><a href="../admin/manage_bookings.php"><i class="fa fa-calendar-check"></i> Manage Booking</a></li>
<<<<<<< HEAD
=======
<li><a href="../admin/notification.php"><i class="fas fa-bell"></i>Notifications</a></li>
<li><a href="../admin/activity_log.php"><i class="fas fa-history"></i>Activity Log</a></li>
<li><a href="../admin/testimonials.php"><i class="fa fa-comments"></i> Manage Testimonials</a></li>
>>>>>>> 57a14d4ef1856b1b796bd0ff4e37f94dbc2c91b4
<li><a href="../admin/contact.php"><i class="fa fa-envelope"></i> Contact Us</a></li>
<li><a href="../admin/users.php"><i class="fa fa-users"></i> Reg Users</a></li>
<li><a href="../admin/page.php"><i class="fa fa-file-alt"></i> Manage Pages</a></li>
<li><a href="../admin/subscriber.php"><i class="fa fa-bell"></i> Manage Subscribers</a></li>

	</ul>
</nav>

<script>
document.querySelectorAll('.has-dropdown > a').forEach(item => {
	item.addEventListener('click', e => {
		e.preventDefault();
		const parent = item.parentElement;
		const submenu = parent.querySelector('ul');
		const icon = item.querySelector('.dropdown-icon');

		submenu.classList.toggle('show');
		parent.classList.toggle('dropdown-open');
	});
});
</script>
