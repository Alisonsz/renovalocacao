type RouteParam = string | number | { id: string | number };
type RouteParams = RouteParam | Record<string, RouteParam>;

const routeMap: Record<string, string> = {
    // Public
    home: '/',
    login: '/login',
    logout: '/logout',
    dashboard: '/dashboard',
    'booking.create': '/reservar/{slug}',
    'booking.store': '/reservar',
    'booking.success': '/reserva-confirmada',
    'products.show': '/produtos/{slug}',

    // Admin
    'admin.dashboard': '/admin',

    'admin.categories.index': '/admin/categories',
    'admin.categories.create': '/admin/categories/create',
    'admin.categories.store': '/admin/categories',
    'admin.categories.edit': '/admin/categories/{category}/edit',
    'admin.categories.update': '/admin/categories/{category}',
    'admin.categories.destroy': '/admin/categories/{category}',

    'admin.products.index': '/admin/products',
    'admin.products.create': '/admin/products/create',
    'admin.products.store': '/admin/products',
    'admin.products.edit': '/admin/products/{product}/edit',
    'admin.products.update': '/admin/products/{product}',
    'admin.products.destroy': '/admin/products/{product}',
    'admin.products.set-primary-image': '/admin/products/{product}/set-primary-image',

    'admin.bookings.index': '/admin/bookings',
    'admin.bookings.update': '/admin/bookings/{booking}',
    'admin.bookings.destroy': '/admin/bookings/{booking}',

    'admin.testimonials.index': '/admin/testimonials',
    'admin.testimonials.create': '/admin/testimonials/create',
    'admin.testimonials.store': '/admin/testimonials',
    'admin.testimonials.edit': '/admin/testimonials/{testimonial}/edit',
    'admin.testimonials.update': '/admin/testimonials/{testimonial}',
    'admin.testimonials.destroy': '/admin/testimonials/{testimonial}',

    'admin.tracking-codes.index': '/admin/tracking-codes',
    'admin.tracking-codes.create': '/admin/tracking-codes/create',
    'admin.tracking-codes.store': '/admin/tracking-codes',
    'admin.tracking-codes.edit': '/admin/tracking-codes/{tracking_code}/edit',
    'admin.tracking-codes.update': '/admin/tracking-codes/{tracking_code}',
    'admin.tracking-codes.destroy': '/admin/tracking-codes/{tracking_code}',

    'admin.settings.index': '/admin/settings',
    'admin.settings.update': '/admin/settings',

    // Settings
    'profile.edit': '/settings/profile',
    'security.edit': '/settings/security',
    'appearance.edit': '/settings/appearance',
    'user-password.update': '/settings/password',
    'profile.update': '/settings/profile',
    'profile.destroy': '/settings/profile',
};

function resolveUrl(pattern: string, params?: RouteParams): string {
    if (params === undefined) {
        return pattern;
    }

    if (typeof params === 'string' || typeof params === 'number') {
        return pattern.replace(/\{[^}]+\}/, String(params));
    }

    if (typeof params === 'object' && 'id' in params) {
        return pattern.replace(/\{[^}]+\}/, String((params as { id: string | number }).id));
    }

    let resolved = pattern;

    for (const [key, value] of Object.entries(params as Record<string, RouteParam>)) {
        const paramValue =
            typeof value === 'object' && 'id' in value ? String(value.id) : String(value);
        resolved = resolved.replace(`{${key}}`, paramValue);
    }

    return resolved;
}

export function route(name: string, params?: RouteParams): string {
    const pattern = routeMap[name];

    if (!pattern) {
        console.error(`[route] Unknown route: "${name}"`);
        return '#';
    }

    return resolveUrl(pattern, params);
}
