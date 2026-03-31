import {createRouter, createWebHistory} from 'vue-router';
import { useAuthStore } from '@/stores/auth';

import LandingView from '@/Views/Home/LandingView.vue';
import LoginView from '@/Views/Account/LoginView.vue';
import SignupView from '@/Views/Account/SignupView.vue';
import MyRequestsView from '@/Views/BookRequest/MyRequestsView.vue';
import MyListings from '@/Views/BookRequest/MyListings.vue';
import CheckoutView from '@/Views/Checkout/CheckoutView.vue';
import DirectMessageView from '@/Views/Chat/DirectMessageView.vue';
import AddBook from '@/Views/Book/AddBooks.vue';
import NotFound from '@/Views/Error/NotFound.vue';
import ReturnView from '@/Views/Checkout/ReturnView.vue';
import ForgotPassword from '@/Views/Account/ForgotPasswordView.vue';
import ResetPasswordView from '@/Views/Account/ResetPasswordView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
        path: '/', name: 'home', component: LandingView,
    },
    {
        path: '/login', name: 'login', component: LoginView,
    },
    {
        path: '/signup', name: 'signup', component: SignupView,
    },
    {
        path: '/forgot-password', name: 'forgot-password', component: ForgotPassword,
    },
    {
        path: '/reset-password', name: 'reset-password', component: ResetPasswordView,
    },
    {
        path: '/myRequests', 
        name: 'my-requests', 
        component: MyRequestsView,
        meta: { requiresAuth: true }
    },
    {
        path: '/myListings', 
        name: 'my-listings', 
        component: MyListings,
        meta: { requiresAuth: true }
    },
    {
        path: '/checkout', 
        name: 'checkout', 
        component: CheckoutView,
        meta: { requiresAuth: true }
    },
    {
        path: '/chat', 
        name: 'direct-message', 
        component: DirectMessageView,
        meta: { requiresAuth: true }
    },
    {
        path: '/addBook', 
        name: 'add-book', 
        component: AddBook,
        meta: { requiresAuth: true }
    },
    {
        path: '/return', 
        name: 'return', 
        component: ReturnView,
        meta: { requiresAuth: true }
    },
    {
        path: '/:catchAll(.*)', name: 'not-found', component: NotFound,
    },

]
});

// Global navigation guard to check authentication
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Wait for auth to be initialized if not already done
  if (authStore.loading) {
    await authStore.fetchLoggedInUser()
  }
  
  // If route requires auth but user is not logged in
  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    // Redirect to login
    next({ name: 'login', query: { redirect: to.path } })
  } else {
    next()
  }
})

export default router;