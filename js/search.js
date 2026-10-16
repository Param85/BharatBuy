document.addEventListener('DOMContentLoaded', function () {
    const searchBtn = document.getElementById('searchBtn');
    const productSearch = document.getElementById('productSearch');
    const resultsDiv = document.getElementById('results');
    const loader = document.getElementById('loader');

    if (!searchBtn || !productSearch) return;

    // Trigger search on click
    searchBtn.addEventListener('click', performSearch);

    // Trigger search on Enter key
    productSearch.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Auto-search if ?q= URL parameter exists
    const urlParams = new URLSearchParams(window.location.search);
    const initialQuery = urlParams.get('q');
    if (initialQuery) {
        productSearch.value = initialQuery;
        performSearch();
    }

    function performSearch() {
        const query = productSearch.value.trim();
        if (!query) return;

        resultsDiv.innerHTML = '';
        loader.style.display = 'block';

        const strictness = localStorage.getItem('search_strictness') || 'strict';
        fetch(`ajax/searchProduct.php?q=${encodeURIComponent(query)}&strictness=${strictness}&_=${Date.now()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                loader.style.display = 'none';
                if (data.error) {
                    resultsDiv.innerHTML = `<p class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> ${data.error}</p>`;
                    return;
                }
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #64748b; padding: 3rem;"><i class="fa-regular fa-face-frown fa-3x" style="margin-bottom: 1rem;"></i><p>No products found matching your search.</p></div>';
                    return;
                }

                data.forEach((item, index) => {
                    const isBest = index === 0; // First item is highest score

                    // Create FontAwesome star rating html
                    const fullStars = Math.floor(item.rating);
                    const halfStar = item.rating % 1 >= 0.5 ? 1 : 0;
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        if (i <= fullStars) {
                            starsHtml += '<i class="fa-solid fa-star"></i>';
                        } else if (i === fullStars + 1 && halfStar) {
                            starsHtml += '<i class="fa-solid fa-star-half-stroke"></i>';
                        } else {
                            starsHtml += '<i class="fa-regular fa-star"></i>';
                        }
                    }

                    function escapeHTML(str) {
                        if (str === null || str === undefined) return '';
                        return String(str)
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#039;');
                    }

                    function sanitizeUrl(url) {
                        if (!url) return '#';
                        const trimmed = String(url).trim();
                        if (/^(javascript|data|vbscript|file):/i.test(trimmed)) {
                            return '#';
                        }
                        return trimmed;
                    }

                    const safeName = escapeHTML(item.name);
                    const safeSite = escapeHTML(item.site);
                    const safeSiteClass = escapeHTML(String(item.site).toLowerCase().replace(/[^a-z0-9]/g, ''));
                    const safeImg = escapeHTML(sanitizeUrl(item.img));
                    const safeUrl = escapeHTML(sanitizeUrl(item.url));
                    const safePrice = Number(item.price) || 0;
                    const safeScore = Number(item.score) || 0;
                    const safeReviews = Number(item.reviews) || 0;
                    const safeId = encodeURIComponent(item.id || '');

                    const card = `
                        <div class="product-card">
                            ${isBest ? '<div class="recommendation-badge"><i class="fa-solid fa-circle-check"></i> BEST OVERALL</div>' : ''}
                            <span class="site-badge badge-${safeSiteClass}">${safeSite}</span>
                            <div style="width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1rem; padding: 0.5rem; overflow: hidden;">
                                <img src="${safeImg}" alt="${safeName}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;" onerror="this.onerror=null; this.src='https://placehold.co/500x500/f5f5f7/1d1d1f?text=Product';">
                            </div>
                            <small>${safeSite}</small>
                            <h3>${safeName}</h3>
                            <div class="price-tag" data-price-inr="${safePrice}">${formatCurrency(safePrice)}</div>
                            <div class="rating-stars">
                                <span>${starsHtml}</span>
                                <small>(${safeReviews} reviews)</small>
                            </div>
                            <div class="score-circle"><i class="fa-solid fa-bolt"></i> Value Score: ${safeScore}/100</div>
                            <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem; width: 100%;">
                                <a href="${safeUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="flex: 1.2; text-align: center; font-size: 0.85rem; padding: 0.6rem 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                    Buy Deal <i class="fa-solid fa-up-right-from-square" style="font-size: 0.75rem;"></i>
                                </a>
                                <a href="product-details.php?id=${safeId}" class="btn btn-outline" style="flex: 1; text-align: center; font-size: 0.85rem; padding: 0.6rem 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                    Details <i class="fa-solid fa-chart-line" style="font-size: 0.75rem;"></i>
                                </a>
                                <button type="button" onclick="handleWishlistClick(this)" data-name="${safeName}" data-site="${safeSite}" data-price="${safePrice}" data-img="${safeImg}" data-url="${safeUrl}" class="btn btn-outline" title="Save to Wishlist" style="display: flex; align-items: center; justify-content: center; width: 40px; padding: 0;">
                                    <i class="fa-regular fa-heart" style="font-size: 1rem;"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    resultsDiv.innerHTML += card;
                });
            })
            .catch(err => {
                loader.style.display = 'none';
                resultsDiv.innerHTML = '<p class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> Error loading search results. Please try again later.</p>';
                console.error(err);
            });
    }
});

function handleWishlistClick(btn) {
    if (!btn) return;
    const name = btn.getAttribute('data-name') || '';
    const site = btn.getAttribute('data-site') || '';
    const price = parseFloat(btn.getAttribute('data-price')) || 0;
    const img = btn.getAttribute('data-img') || '';
    const url = btn.getAttribute('data-url') || '';
    saveToWishlist(btn, name, site, price, img, url);
}

function saveToWishlist(btnOrName, site, price, img, url) {
    let btn, nameVal, siteVal, priceVal, imgVal, urlVal;
    if (typeof btnOrName === 'object' && btnOrName !== null && btnOrName.nodeType) {
        btn = btnOrName;
        nameVal = site;
        siteVal = price;
        priceVal = img;
        imgVal = url;
        urlVal = arguments[5] || '';
    } else {
        btn = (typeof event !== 'undefined' && event && event.currentTarget) ? event.currentTarget : document.activeElement;
        nameVal = btnOrName;
        siteVal = site;
        priceVal = price;
        imgVal = img;
        urlVal = url;
    }

    const icon = btn ? btn.querySelector('i') : null;

    if (icon) {
        icon.classList.remove('fa-regular', 'fa-heart');
        icon.classList.add('fa-solid', 'fa-heart', 'fa-beat');
    }
    if (btn) {
        btn.style.color = '#ef4444';
        btn.style.borderColor = '#ef4444';
    }

    fetch('ajax/wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: nameVal, site: siteVal, price: priceVal, img: imgVal, url: urlVal })
    })
        .then(res => {
            if (!res.ok) {
                throw new Error('Wishlist API error');
            }
            return res.json();
        })
        .then(data => {
            if (icon) icon.classList.remove('fa-beat');
            if (data.success) {
                alert('Product added to your wishlist!');
            } else {
                if (icon) {
                    icon.classList.remove('fa-solid', 'fa-heart');
                    icon.classList.add('fa-regular', 'fa-heart');
                }
                if (btn) {
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }
                alert(data.error || 'Failed to save to wishlist.');
            }
        })
        .catch(err => {
            if (icon) {
                icon.classList.remove('fa-beat');
                icon.classList.remove('fa-solid', 'fa-heart');
                icon.classList.add('fa-regular', 'fa-heart');
            }
            if (btn) {
                btn.style.color = '';
                btn.style.borderColor = '';
            }
            console.error(err);
            alert('Failed to save product to wishlist.');
        });
}
