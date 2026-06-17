import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Shared fetch helper for cart actions
function cartFetch(productId, quantity) {
    return fetch(`/cart/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ quantity }),
    }).then((r) => r.json());
}

// Used in x-product-card: add 1 item directly
Alpine.data('addToCart', (productId) => ({
    adding: false,
    add() {
        this.adding = true;
        cartFetch(productId, 1)
            .then((data) => {
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                    window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: data.message } }));
                }
            })
            .catch(() => {
                window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Có lỗi xảy ra, vui lòng thử lại.' } }));
            })
            .finally(() => { this.adding = false; });
    },
}));

// Used in products/show.blade.php: quantity selector + add
Alpine.data('productDetail', (productId, maxStock) => ({
    qty: 1,
    max: maxStock,
    adding: false,
    add() {
        this.adding = true;
        cartFetch(productId, this.qty)
            .then((data) => {
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                    window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: data.message } }));
                }
            })
            .catch(() => {
                window.dispatchEvent(new CustomEvent('show-toast', { detail: { message: 'Có lỗi xảy ra, vui lòng thử lại.' } }));
            })
            .finally(() => { this.adding = false; });
    },
}));

Livewire.start();
