import { createRouter, createWebHistory } from "vue-router";
import AuthLayout from "@/layout/AuthLayout.vue";
import Master from "@/layout/Master.vue";
import { checkPermissions } from "@/utils/helper.js";

const Login = () => import("@/pages/Login.vue");
const Dashboard = () => import("@/pages/Dashboard.vue");
const ProjectList = () => import("@/pages/projects/List.vue");
const ProjectDetail = () => import("@/pages/projects/Detail.vue");
const TaskList = () => import("@/pages/task/List.vue");
const TaskAdd = () => import("@/pages/task/New.vue");
const TaskEdit = () => import("@/pages/task/Edit.vue");
const TaskDetail = () => import("@/pages/task/Detail.vue");
const RoleLists = () => import("@/pages/role/List.vue");
const RoleCreate = () => import("@/pages/role/New.vue");
const UserList = () => import("@/pages/users/List.vue");
// const UserCreate = () => import("@/pages/users/New.vue");
const RoleEdit = () => import("@/pages/role/Edit.vue");
const UserDetail = () => import("@/pages/users/Detail.vue");
const Profile = () => import("@/pages/Profile.vue");
const UserRolePermissionList = () =>
    import("@/pages/user-role-permission/List.vue");
const UserRolePermissionCreate = () =>
    import("@/pages/user-role-permission/New.vue");
const UserRolePermissionEdit = () =>
    import("@/pages/user-role-permission/Edit.vue");

const routes = [
    {
        path: "/auth",
        redirect: "/login",
        component: AuthLayout,
        meta: { auth: false },
        children: [
            {
                path: "/login",
                name: "login",
                component: Login,
                meta: {
                    auth: false,
                    title: "Login",
                },
            },
        ],
    },

    {
        path: "/",
        redirect: "/dashboard",
        component: Master,
        meta: { auth: true },
        children: [
            {
                path: "/",
                name: "dashboard",
                component: Dashboard,
                meta: {
                    auth: true,
                    title: "Dashboard",
                },
            },
            {
                path: "/project/list",
                name: "project-list",
                component: ProjectList,
                meta: {
                    auth: true,
                    title: "Project List",
                },
            },
            {
                path: "/project/:id",
                name: "project-detail",
                component: ProjectDetail,
                meta: {
                    auth: true,
                    title: "Project Detail",
                },
            },
            {
                path: "/task/list",
                name: "task-list",
                component: TaskList,
                meta: {
                    auth: true,
                    title: "Task List",
                },
            },
            {
                path: "/task/add",
                name: "new-task",
                component: TaskAdd,
                meta: {
                    auth: true,
                    title: "New Task",
                },
            },
            {
                path: "/task/edit/:id",
                name: "edit-task",
                component: TaskEdit,
                meta: {
                    auth: true,
                    title: "Edit Task",
                },
            },
            {
                path: "/task/detail/:id",
                name: "task-detail",
                component: TaskDetail,
                meta: {
                    auth: true,
                    title: "Task Detail",
                },
            },
            {
                path: "/roles",
                name: "roles",
                component: RoleLists,
                meta: {
                    auth: true,
                    title: "Role List",
                },
            },
            {
                path: "/role/create",
                name: "role-create",
                component: RoleCreate,
                meta: {
                    auth: true,
                    title: "New Role",
                },
            },
            {
                path: "/role-edit/:id",
                name: "role-edit",
                component: RoleEdit,
                meta: {
                    auth: true,
                    title: "Edit Role",
                },
            },
            {
                path: "/userrolepermission",
                name: "userrolepermission-list",
                component: UserRolePermissionList,
                meta: {
                    auth: true,
                    title: "User Role Permissions",
                },
            },
            {
                path: "/userrolepermission/create",
                name: "userrolepermission-create",
                component: UserRolePermissionCreate,
                meta: {
                    auth: true,
                    title: "Create User Role Permission",
                },
            },
            {
                path: "/userrolepermission/:id",
                name: "userrolepermission-edit",
                component: UserRolePermissionEdit,
                meta: {
                    auth: true,
                    title: "Edit User Role Permission",
                },
            },
            {
                path: "/user/list",
                name: "user-list",
                component: UserList,
                meta: {
                    auth: true,
                    title: "User List",
                },
            },
            {
                path: "/user/:id",
                name: "user-detail",
                component: UserDetail,
                meta: {
                    auth: true,
                    title: "User Detail",
                },
            },
            {
                path: "/profile",
                name: "profile",
                component: Profile,
                meta: {
                    auth: true,
                    title: "User Profile",
                },
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Middleware to check authentication
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("token");
    if (to.meta.title) {
        document.title = `TaskAssignee | ${to.meta.title}`;
    }

    console.log("logging to here", to);
    if (to.meta.auth && !token) {
        // Redirect to login if not authenticated
        console.log("Not authenticated, redirecting to login...");
        next({ name: "login" });
    } else if (to.name === "login" && token) {
        // Prevent access to login route if already authenticated
        // Check if user has dashboard permission
        if (checkPermissions("dashboard.read")) {
            next({ name: "dashboard" });
        } else {
            // Find the first route the user has permission to access
            const firstPermittedRoute = routes.find((route) => {
                if (!route.meta || !route.meta.permission) return false;
                return checkPermissions(route.meta.permission);
            });
            if (firstPermittedRoute) {
                next({ name: firstPermittedRoute.name });
            } else {
                toast.error("You do not have permission to access any page.");
                setTimeout(() => {
                    clearLocalStorage();
                    next({ name: "login" });
                }, 2000);
            }
        }
    } else if (to.meta.permission && !checkPermissions(to.meta.permission)) {
        // If route requires permission and user doesn't have it
        toast.error("You do not have permission to access this page.");
        setTimeout(() => {
            clearLocalStorage();
            next({ name: "login" });
        }, 2000);
        // Cancel navigation
    } else if (from.name == "mrn-add" || from.name == "mrn-edit") {
        localStorage.removeItem("mrnDetailProducts");
        localStorage.removeItem("productsToRemove");
        next();
    } else {
        // Proceed to the route
        next();
    }
});

export default router;
