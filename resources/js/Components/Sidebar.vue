<script setup>
import {Link} from '@inertiajs/vue3';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
</script>

<template>
	<aside class="sidebar py-4 bg-white hidden md:block mt-16 h-full">
		<!-- Sidebar content goes here -->
		<ul>
			<li class="nav-item" :class="route().current('dashboard') ? 'active-link' : ''">
				<Link :href="route('dashboard')" :active="route().current('dashboard')" class="nav-link">
					<font-awesome-icon icon="house-chimney" class="mx-1"/>
					Dashboard
				</Link>
			</li>
			<li class="nav-item"
				:class="route().current('products.index') || route().current('product.edit') || route().current('package.index') || route().current('product.create') || route().current('inventoryLogs.index') || route().current('inventoryLogs.create')? 'active-link' : ''">
				<Link :href="route('products.index')" :active="route().current('products.index')" class="nav-link">
					<font-awesome-icon icon="warehouse" class="mx-1"/>
					Inventory
				</Link>
			</li>
			<li class="nav-item"
				:class="route().current('orders.index') || route().current('order.show') || route().current('order.create') ? 'active-link' : ''">
				<Link :href="route('orders.index')" :active="route().current('orders.index')" class="nav-link">
					<font-awesome-icon icon="circle-dollar-to-slot" class="mx-1"/>
					Sales Order
				</Link>
			</li>
			<li class="nav-item"
				:class="route().current('campaigns.index') || route().current('campaign.create') ? 'active-link' : ''">
				<Link :href="route('campaigns.index')" :active="route().current('campaigns.index')" class="nav-link">
					<font-awesome-icon icon="fire" class="mx-1"/>
					Campaign
				</Link>
			</li>
            <li class="nav-item"
                :class="route().current('documents.index') || route().current('document.create') ? 'active-link' : ''">
                <Link :href="route('documents.index')" :active="route().current('documents.index')" class="nav-link">
                    <font-awesome-icon icon="file-import" class="mx-1"/>
                    Document
                </Link>
            </li>
		</ul>
		<hr>
		<p class="pt-6 px-6 text-gray-400 text-sm font-bold">
			Users
		</p>
		<ul>
			<li class="nav-item" :class="route().current('customers.index') || route().current('customer.create')  ? 'active-link' : ''">
				<Link :href="route('customers.index')" :active="route().current('customers.index')" class="nav-link">
					<font-awesome-icon icon="sitemap" class="mx-1"/>
					Customer
				</Link>
			</li>
			<li class="nav-item" :class="route().current('sellers.index') || route().current('sellers.create')  ? 'active-link' : ''">
				<Link :href="route('sellers.index')" :active="route().current('sellers.index')" class="nav-link">
					<font-awesome-icon icon="image-portrait" class="mx-1"/>
					Dealer
				</Link>
			</li>
			<li class="nav-item" :class="route().current('groups.index') || route().current('groups.create')  ? 'active-link' : ''">
				<Link :href="route('groups.index')" :active="route().current('groups.index')" class="nav-link">
					<font-awesome-icon icon="people-roof" class="mx-1"/>
					Group
				</Link>
			</li>
		</ul>
		<hr>
		<p class="pt-6 px-6 text-gray-400 text-sm font-bold">
			Settings
		</p>
		<ul>
			<li class="nav-item" :class="route().current('postages.index') ? 'active-link' : ''">
				<Link :href="route('postages.index')" :active="route().current('postages.index')" class="nav-link">
					<font-awesome-icon icon="location-dot" class="mx-1"/>
					Postage
				</Link>
			</li>
			<li class="nav-item" :class="route().current('delivery.index') ? 'active-link' : ''">
				<Link :href="route('delivery.index')" :active="route().current('delivery.index')" class="nav-link">
					<font-awesome-icon icon="truck" class="mx-1"/>
					Delivery Method
				</Link>
			</li>
            <li class="nav-item" :class="route().current('announcements.index') ? 'active-link' : ''">
                <Link :href="route('announcements.index')" :active="route().current('announcements.index')" class="nav-link">
                    <font-awesome-icon icon="volume-high" class="mx-1"/>
                    Announcement
                </Link>
            </li>
		</ul>
	</aside>
</template>

<style scoped>
.sidebar {
	left: 0;
	border-width: 0 1px 0 0;
	max-width: 250px !important;
	position: fixed;
	overflow-y: auto;
	top: 0;
	width: 100%;
	padding-right: 0;
	padding-left: 0;
    padding-bottom: 5rem;
}

.nav-item {
	margin: 8px 16px;
	border-radius: 8px;
}

.nav-item:hover {
	background-color: #f1f1f1;
	cursor: pointer;
	color: #ef4444;
}

.active-link {
	color: #d71d28;
	font-weight: bold;
	border-left: 5px solid #d71d28;
	border-radius: 0;
}

.nav-link {
	display: block; /* Make the link a block-level element */
	padding: 12px;
	text-decoration: none;
	color: inherit;
}
</style>
