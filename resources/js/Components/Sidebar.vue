<script setup>
import {Link} from '@inertiajs/vue3';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
</script>

<template>
    <!--    <nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white hidden md:block mt-16 h-full" id="sidenav-main">-->
    <!--        <div class="scroll-wrapper scrollbar-inner" style="position: relative;">-->
    <!--            <div class="scrollbar-inner scroll-content mb-0 mr-0 bg-black">-->
    <!--                <div class="navbar-inner bg-black">-->
    <!--                    <div class="collapse navbar-collapse" id="sidenav-collapse-main">-->
    <!--                        <ul class="navbar-nav">-->
    <!--                            <li class="nav-item">-->
    <!--                                <Link :href="route('dashboard')" class="text-white">-->
    <!--                                    Dashboard-->
    <!--                                </Link>-->
    <!--                            </li>-->
    <!--                            <li class="nav-item">-->
    <!--                                <Link :href="route('dashboard')">-->
    <!--                                    Dashboard-->
    <!--                                </Link>-->
    <!--                            </li>-->
    <!--                            <li class="nav-item">-->
    <!--                                <Link :href="route('dashboard')">-->
    <!--                                    Dashboard-->
    <!--                                </Link>-->
    <!--                            </li>-->
    <!--                        </ul>-->

    <!--                        <hr class="my-3">-->

    <!--                        <ul class="navbar-nav mb-md-3">-->
    <!--                            <li class="nav-item">-->
    <!--                                <Link :href="route('dashboard')">-->
    <!--                                    Log out-->
    <!--                                </Link>-->
    <!--                            </li>-->
    <!--                        </ul>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </nav>-->
    <aside class="sidebar py-4 bg-white hidden md:block mt-16 h-full">
        <!-- Sidebar content goes here -->
        <ul>
            <li class="nav-item" :class="route().current('dashboard') ? 'active-link' : ''">
                <Link :href="route('dashboard')" :active="route().current('dashboard')">
                    <font-awesome-icon icon="house-chimney" class="mx-1"/>
                    Dashboard
                </Link>
            </li>
            <li class="nav-item">
                <Link :href="route('dashboard')">
                    <font-awesome-icon icon="boxes-stacked" class="mx-1"/>
                    Product
                </Link>
            </li>
            <li class="nav-item">
                <Link :href="route('dashboard')">
                    <font-awesome-icon icon="warehouse" class="mx-1"/>
                    Inventory
                </Link>
            </li>
            <li class="nav-item">
                <Link :href="route('dashboard')">
                    <font-awesome-icon icon="circle-dollar-to-slot" class="mx-1"/>
                    Sales Order
                </Link>
            </li>
            <li class="nav-item">
                <Link :href="route('dashboard')">
                    <font-awesome-icon icon="sitemap" class="mx-1"/>
                    Members
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
}

.nav-item {
    margin-left: 16px;
    margin-right: 16px;
    padding: 12px;
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
</style>
