import { route as routeHelper } from '@/lib/route';

export function useRoute() {
    return {
        route: routeHelper,
    };
}
