import {createRouter, createWebHistory} from 'vue-router';

import LandingView from '@/Views/Home/LandingView.vue';
import LoginView from '@/Views/Account/LoginView.vue';
import SignupView from '@/Views/Account/SignupView.vue';
import MyRequestsView from '@/Views/BookRequest/MyRequestsView.vue';
import MyListings from '@/Views/BookRequest/MyListings.vue';
import CheckoutView from '@/Views/Checkout/CheckoutView.vue';

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
        path: '/myRequests', name: 'my-requests', component: MyRequestsView,
    },
    {
        path: '/myListings', name: 'my-listings', component: MyListings,
    },
    {
        path: '/checkout', name: 'checkout', component: CheckoutView,
    },
]
});

export default router;