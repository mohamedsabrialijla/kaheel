// Admin Pages
import AuthLayout from './components/Website/auth/AuthLayout.vue';
import LoginAdmin from './components/Website/adminAuth/Login.vue';
import Dashboard from './components/Dashboard/pages/Dashboard.vue';
import KaheelSection from './components/Dashboard/pages/KaheelSection.vue';
import Suppliers from './components/Dashboard/pages/Suppliers.vue';
import Packages from './components/Dashboard/pages/Packages.vue';
import Settings from './components/Dashboard/pages/Settings.vue';
import KaheelRoles from './components/Dashboard/pages/RolesPage.vue';
import Staff from './components/Dashboard/pages/StaffPage.vue';
import Alternatives from './components/Dashboard/pages/Alternatives.vue';

import Admins from './components/Dashboard/pages/UserManagement/Admins.vue';
import Users from './components/Dashboard/pages/UserManagement/Users.vue';
import Roles from './components/Dashboard/pages/UserManagement/Roles.vue';
import Permissions from './components/Dashboard/pages/UserManagement/Permissions.vue';

// HR Pages
import Department from './components/Dashboard/pages/HR/Department.vue';
import Section from './components/Dashboard/pages/HR/Section.vue';
import Position from './components/Dashboard/pages/HR/Position.vue';
import Employee from './components/Dashboard/pages/HR/Employee.vue';
import EmployeePosition from './components/Dashboard/pages/HR/EmployeePosition.vue';
import PositionHistory from './components/Dashboard/pages/HR/PositionHistory.vue';

// Master Data Pages
import ItemsCategories from './components/Dashboard/pages/MasterData/ItemsCategories.vue';
import ItemsObjects from './components/Dashboard/pages/MasterData/ItemsObjects.vue';
import ResumeTemplate from './components/Dashboard/pages/MasterData/ResumeTemplate.vue';

// Settings Pages
import Page from './components/Dashboard/pages/Setting/Page.vue';
import Edit from './components/Dashboard/pages/Setting/Edit.vue';
import Contact from './components/Dashboard/pages/Setting/contact.vue';
import Report from './components/Dashboard/pages/Setting/Report.vue';
import Notification from './components/Dashboard/pages/Setting/Notification.vue';

//Ads pages
import Ad from './components/Dashboard/pages/Ads/Ad.vue';


// Orders 
import Order from './components/Dashboard/pages/Orders/Order.vue';



export default [
 {
        path: '/:locale/admin/login',
        component: AuthLayout,
        children: [
            { path: '', component: LoginAdmin, meta: { hideHeaderFooter: true } }
        ]
    },

    // Admin Dashboard Routes
    { 
        path: '/:locale/dashboard', 
        component: Dashboard, 
        meta: { requiresAuth: true, role: 'admin' } 
    },
    { path: '/:locale/dashboard/orders', component: KaheelSection, meta: { requiresAuth: true, role: 'admin', kaheel: 'orders' } },
    { path: '/:locale/dashboard/staff', component: Staff, meta: { requiresAuth: true, role: 'admin', kaheel: 'staff' } },
    { path: '/:locale/dashboard/suppliers', component: Suppliers, meta: { requiresAuth: true, role: 'admin', kaheel: 'suppliers' } },
    { path: '/:locale/dashboard/packages', component: Packages, meta: { requiresAuth: true, role: 'admin', kaheel: 'packages' } },
    { path: '/:locale/dashboard/alternatives', component: Alternatives, meta: { requiresAuth: true, role: 'admin', kaheel: 'alternatives' } },
    { path: '/:locale/dashboard/roles', component: KaheelRoles, meta: { requiresAuth: true, role: 'admin', kaheel: 'roles' } },
    { path: '/:locale/dashboard/settings', component: Settings, meta: { requiresAuth: true, role: 'admin', kaheel: 'settings' } },
    { 
        path: '/:locale/dashboard/user_management/users', 
        component: Users, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Users Management' } 
    },
    { 
        path: '/:locale/dashboard/user_management/admins', 
        component: Admins, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Admins Management' } 
    },
    { 
        path: '/:locale/dashboard/user_management/roles', 
        component: Roles, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Roles Management' } 
    },
    { 
        path: '/:locale/dashboard/user_management/permissions', 
        component: Permissions, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Permissions Management' } 
    },

    // HR Routes
    { 
        path: '/:locale/dashboard/HR/departments', 
        component: Department, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Departments Management' } 
    },
    { 
        path: '/:locale/dashboard/HR/sections', 
        component: Section, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Sections Management' } 
    },
    { 
        path: '/:locale/dashboard/HR/positions', 
        component: Position, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Positions Management' } 
    },
    { 
        path: '/:locale/dashboard/HR/employees', 
        component: Employee, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Employees Management' } 
    },
    { 
        path: '/:locale/dashboard/HR/employee_positions', 
        component: EmployeePosition, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Employee Positions Management' } 
    },
    { 
        path: '/:locale/dashboard/HR/position_history', 
        component: PositionHistory, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Position History Management' } 
    },

    // Master Data Routes
    {
        path: '/:locale/dashboard/master_data/category/:category',
        component: ItemsCategories,
        props: true,
        meta: { requiresAuth: true, role: 'admin' },
        beforeEnter: (to, from, next) => {
            to.meta.breadcrumb = `${to.params.category.charAt(0).toUpperCase() + to.params.category.slice(1)} Management`;
            next();
        }
    },
    { 
        path: '/:locale/dashboard/master_data/resume_template', 
        component: ResumeTemplate, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Resume Templates Management' } 
    },
    {
        path: '/:locale/dashboard/master_data/:object',
        component: ItemsObjects,
        props: true,
        meta: { requiresAuth: true, role: 'admin' },
        beforeEnter: (to, from, next) => {
            to.meta.breadcrumb = `${to.params.object.charAt(0).toUpperCase() + to.params.object.slice(1)} Management`;
            next();
        }
    },



    // Settings Routes
    { 
        path: '/:locale/dashboard/setting/pages', 
        component: Page, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Pages Management' } 
    },
    { 
        path: '/:locale/dashboard/setting/edit', 
        component: Edit, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Settings Management' } 
    },


    { 
        path: '/:locale/dashboard/setting/contact', 
        component: Contact, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Contact Management' } 
    },

    { 
        path: '/:locale/dashboard/setting/report', 
        component: Report, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Report Management' } 
    },

    { 
        path: '/:locale/dashboard/setting/notifications', 
        component: Notification, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Notifications Management' } 
    },


    // Ads Routes


    { 
        path: '/:locale/dashboard/ads/ad', 
        component: Ad, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Ads Management' } 
    },


    { 
        path: '/:locale/dashboard/orders/order', 
        component: Order, 
        meta: { requiresAuth: true, role: 'admin', breadcrumb: 'Orders Management' } 
    },


];