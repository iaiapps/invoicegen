 <!-- Sidebar Overlay (Mobile) -->
 <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

 <aside class="sidebar" id="sidebar">
     <div class="sidebar-header">
         <a href="{{ route('dashboard') }}" class="sidebar-brand">
             <i class="bi bi-receipt-cutoff"></i>
             <span class="sidebar-brand-text">InvoiceGen</span>
         </a>
     </div>

     <nav class="sidebar-menu">
         {{-- ADMIN (Super Admin) Menu --}}
         @role('admin')
             <a href="{{ route('dashboard') }}"
                 class="sidebar-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                 <i class="bi bi-speedometer2"></i>
                 <span class="sidebar-menu-item-text">Dashboard</span>
             </a>

             <a href="{{ route('admin.users.index') }}"
                 class="sidebar-menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                 <i class="bi bi-people"></i>
                 <span class="sidebar-menu-item-text">Kelola User</span>
             </a>

             <a href="{{ route('admin.subscriptions.index') }}"
                 class="sidebar-menu-item {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                 <i class="bi bi-credit-card"></i>
                 <span class="sidebar-menu-item-text">Subscription</span>
             </a>

             <a href="{{ route('admin.payments.index') }}"
                 class="sidebar-menu-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                 <i class="bi bi-cash-stack"></i>
                 <span class="sidebar-menu-item-text">Pembayaran</span>
             </a>

             {{-- <a href="#" class="sidebar-menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                 <i class="bi bi-graph-up"></i>
                 <span class="sidebar-menu-item-text">Laporan</span>
             </a> --}}

             <div class="sidebar-divider"></div>

             <a href="{{ route('admin.settings') }}"
                 class="sidebar-menu-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                 <i class="bi bi-gear"></i>
                 <span class="sidebar-menu-item-text">Pengaturan</span>
             </a>
         @endrole

         {{-- USER (Tenant/Pemilik Toko) Menu --}}
         @role('user')
             <a href="{{ route('dashboard') }}"
                 class="sidebar-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                 <i class="bi bi-house-door"></i>
                 <span class="sidebar-menu-item-text">Dashboard</span>
             </a>

             <a href="{{ route('invoices.index') }}"
                 class="sidebar-menu-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                 <i class="bi bi-file-earmark-text"></i>
                 <span class="sidebar-menu-item-text">Invoice</span>
             </a>

             <a href="{{ route('customers.index') }}"
                 class="sidebar-menu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                 <i class="bi bi-people"></i>
                 <span class="sidebar-menu-item-text">Customer</span>
             </a>

             <a href="{{ route('products.index') }}"
                 class="sidebar-menu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                 <i class="bi bi-box-seam"></i>
                 <span class="sidebar-menu-item-text">Produk</span>
             </a>

             <div class="sidebar-divider"></div>

             <a href="{{ route('settings') }}"
                 class="sidebar-menu-item {{ request()->routeIs('settings') ? 'active' : '' }}">
                 <i class="bi bi-shop"></i>
                 <span class="sidebar-menu-item-text">Pengaturan Toko</span>
             </a>

             <a href="{{ route('subscription.index') }}"
                 class="sidebar-menu-item {{ request()->routeIs('subscription') ? 'active' : '' }}">
                 <i class="bi bi-credit-card"></i>
                 <span class="sidebar-menu-item-text">Langganan</span>
             </a>
         @endrole
     </nav>

     @include('layouts.partials.footer')
 </aside>

 @push('scripts')
     <script>
         // Sidebar Toggle Function
         function toggleSidebar() {
             const sidebar = document.getElementById('sidebar');
             const overlay = document.getElementById('sidebarOverlay');
             const mainContent = document.getElementById('mainContent');
             const isMobile = window.innerWidth < 768;

             if (isMobile) {
                 // Mobile: Show/Hide sidebar with overlay
                 sidebar.classList.toggle('show');
                 overlay.classList.toggle('show');
             } else {
                 // Desktop: Collapse/Expand sidebar
                 sidebar.classList.toggle('collapsed');
                 mainContent.classList.toggle('sidebar-collapsed');

                 // Save state to localStorage
                 const isCollapsed = sidebar.classList.contains('collapsed');
                 localStorage.setItem('sidebarCollapsed', isCollapsed);
             }
         }

         // Load sidebar state on page load (Desktop only)
         document.addEventListener('DOMContentLoaded', function() {
             if (window.innerWidth >= 768) {
                 const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                 if (isCollapsed) {
                     document.getElementById('sidebar').classList.add('collapsed');
                     document.getElementById('mainContent').classList.add('sidebar-collapsed');
                 }
             }
         });

         // Handle window resize
         let resizeTimer;
         window.addEventListener('resize', function() {
             clearTimeout(resizeTimer);
             resizeTimer = setTimeout(function() {
                 const sidebar = document.getElementById('sidebar');
                 const overlay = document.getElementById('sidebarOverlay');
                 const mainContent = document.getElementById('mainContent');

                 if (window.innerWidth < 768) {
                     // Mobile mode
                     sidebar.classList.remove('collapsed');
                     mainContent.classList.remove('sidebar-collapsed');

                     // Close sidebar if open
                     sidebar.classList.remove('show');
                     overlay.classList.remove('show');
                 } else {
                     // Desktop mode
                     sidebar.classList.remove('show');
                     overlay.classList.remove('show');

                     // Restore collapsed state from localStorage
                     const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                     if (isCollapsed) {
                         sidebar.classList.add('collapsed');
                         mainContent.classList.add('sidebar-collapsed');
                     }
                 }
             }, 250);
         });

         // Close mobile sidebar when clicking overlay
         document.getElementById('sidebarOverlay').addEventListener('click', function() {
             if (window.innerWidth < 768) {
                 document.getElementById('sidebar').classList.remove('show');
                 this.classList.remove('show');
             }
         });
     </script>
 @endpush
