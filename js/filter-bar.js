/**
 * Unified filter bar + load more controller
 *
 * Single source of truth for grid state: filters, pagination, loaded IDs.
 * Uses Abilities API REST endpoints with legacy AJAX fallback.
 *
 * @since 2.3.0
 */
document.addEventListener('DOMContentLoaded', function () {
    const loadMoreButton = document.getElementById('load-more');
    const postGrid = document.getElementById('post-grid');
    const filterBar = document.getElementById('filter-bar');

    if (!postGrid) {
        return;
    }

    /* ------------------------------------------------------------------ */
    /*  State                                                              */
    /* ------------------------------------------------------------------ */

    let isImageGallery = document.getElementById('filter-image-gallery')
        ? document.getElementById('filter-image-gallery').value === '1'
        : false;

    const path = window.location.pathname;
    if (!isImageGallery && (/\/images\/?$/.test(path) || path === '/images/')) {
        isImageGallery = true;
    }

    const isAllSiteImages = isImageGallery && (path === '/images/' || path === '/images');

    function determineInitialType() {
        var activeBtn = document.querySelector('.type-btn.active');
        if (activeBtn && activeBtn.dataset && activeBtn.dataset.type) {
            return activeBtn.dataset.type;
        }
        if (isImageGallery) return 'images';
        return document.querySelector('.type-btn[data-type="all"]') ? 'all' : 'posts';
    }

    var currentFilters = {
        sort_by: 'random',
        post_type_filter: determineInitialType(),
        category: document.getElementById('filter-category') ? document.getElementById('filter-category').value : '',
        tag: document.getElementById('filter-tag') ? document.getElementById('filter-tag').value : '',
        search: document.getElementById('filter-search') ? document.getElementById('filter-search').value : ''
    };

    var currentPage = 1;
    var loadedPostIds = [];
    var loadedImageIds = [];

    /* ------------------------------------------------------------------ */
    /*  Gallery column management (absorbed from load-more.js)             */
    /* ------------------------------------------------------------------ */

    var galleryColumns = isImageGallery
        ? Array.from(postGrid.querySelectorAll('.gallery-col'))
        : [];

    function desiredColCount() {
        return SaraiGalleryUtils.getColumnCount();
    }

    function reflowColumnsTo(count) {
        if (!isImageGallery) return;
        var figs = Array.from(postGrid.querySelectorAll('figure.gallery-item'));
        postGrid.innerHTML = '';
        var cols = SaraiGalleryUtils.createColumns(count);
        cols.forEach(function (col) { postGrid.appendChild(col); });
        SaraiGalleryUtils.distributeFigures(figs, cols);
        galleryColumns = cols;
    }

    // Ensure initial column count matches viewport.
    if (isImageGallery && galleryColumns.length && galleryColumns.length !== desiredColCount()) {
        reflowColumnsTo(desiredColCount());
    }

    // Debounced resize handler.
    var resizeTimer;
    window.addEventListener('resize', function () {
        if (!isImageGallery) return;
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            var want = desiredColCount();
            var current = postGrid.querySelectorAll('.gallery-col').length || 0;
            if (current !== want) {
                reflowColumnsTo(want);
            }
            galleryColumns = Array.from(postGrid.querySelectorAll('.gallery-col'));
        }, 150);
    });

    function appendFiguresBalanced(figs) {
        if (!isImageGallery) return false;
        galleryColumns = Array.from(postGrid.querySelectorAll('.gallery-col'));

        if (galleryColumns.length === 0) {
            var want = desiredColCount();
            var existingFigs = Array.from(postGrid.querySelectorAll('figure.gallery-item'));
            postGrid.innerHTML = '';
            var cols = SaraiGalleryUtils.createColumns(want);
            cols.forEach(function (col) { postGrid.appendChild(col); });
            galleryColumns = cols;
            SaraiGalleryUtils.distributeFigures(existingFigs, galleryColumns);
        }

        SaraiGalleryUtils.distributeFigures(figs, galleryColumns);
        return true;
    }

    /* ------------------------------------------------------------------ */
    /*  Collect already-loaded content IDs                                 */
    /* ------------------------------------------------------------------ */

    function initializeLoadedContent() {
        if (isImageGallery) {
            loadedImageIds = Array.from(postGrid.querySelectorAll('.gallery-item img')).map(function (img) {
                var match = img.className.match(/wp-image-(\d+)/);
                return match ? match[1] : '';
            }).filter(function (id) { return id !== ''; });
        } else {
            loadedPostIds = Array.from(postGrid.querySelectorAll('article')).map(function (post) {
                return post.id.replace('post-', '');
            });
        }
    }

    /* ------------------------------------------------------------------ */
    /*  UI helpers                                                         */
    /* ------------------------------------------------------------------ */

    function updateFilterStates() {
        document.querySelectorAll('.sort-btn').forEach(function (btn) {
            btn.classList.remove('active');
            if (btn.dataset.sort === currentFilters.sort_by) {
                btn.classList.add('active');
            }
        });

        document.querySelectorAll('.type-btn').forEach(function (btn) {
            btn.classList.remove('active');
            if (btn.dataset.type === currentFilters.post_type_filter) {
                btn.classList.add('active');
            }
            if (isImageGallery && btn.dataset.type === 'images') {
                btn.classList.add('active');
            }
        });
    }

    function setLoadMoreState(text, disabled, visible) {
        if (!loadMoreButton) return;
        loadMoreButton.textContent = text;
        loadMoreButton.disabled = disabled;
        if (typeof visible !== 'undefined') {
            loadMoreButton.style.display = visible ? 'block' : 'none';
        }
    }

    /* ------------------------------------------------------------------ */
    /*  REST fetch (primary) with AJAX fallback                            */
    /* ------------------------------------------------------------------ */

    function buildInput(append) {
        var input = {
            sort_by: currentFilters.sort_by,
            post_type_filter: currentFilters.post_type_filter,
            loaded_ids: (isImageGallery ? loadedImageIds : loadedPostIds).map(Number)
        };
        if (!append) {
            input.loaded_ids = [];
        }
        if (currentFilters.category) input.category = currentFilters.category;
        if (currentFilters.tag) input.tag = currentFilters.tag;
        if (currentFilters.search) input.search = currentFilters.search;
        if (isImageGallery && isAllSiteImages) input.all_site = true;

        // Also check load-more button data attributes for search.
        if (!input.search && loadMoreButton && loadMoreButton.getAttribute('data-search')) {
            input.search = loadMoreButton.getAttribute('data-search');
        }

        return input;
    }

    function fetchViaRest(append) {
        var abilityId = isImageGallery ? 'sarai-chinwag/query-images' : 'sarai-chinwag/query-posts';
        var url = sarai_chinwag_ajax.restUrl + abilityId + '/run';

        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': sarai_chinwag_ajax.nonce
            },
            body: JSON.stringify({ input: buildInput(append) })
        })
        .then(function (r) {
            if (!r.ok) throw new Error('REST ' + r.status);
            return r.json();
        })
        .then(function (data) {
            // Response may be {output: {html, count}} or {html, count} directly
            var output = data.output || data;
            return { html: output.html || '', count: output.count || 0 };
        });
    }

    function fetchViaAjax(append) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            var data = new FormData();
            var input = buildInput(append);

            data.append('action', isImageGallery ? 'filter_images' : 'filter_posts');
            data.append('nonce', sarai_chinwag_ajax.ajax_nonce || sarai_chinwag_ajax.nonce);
            data.append('page', currentPage + (append ? 1 : 0));
            data.append('sort_by', input.sort_by);
            data.append('post_type_filter', input.post_type_filter);

            if (isImageGallery) {
                data.append('loadedImages', JSON.stringify(input.loaded_ids.map(String)));
                if (input.all_site) data.append('all_site', 'true');
            } else {
                data.append('loadedPosts', JSON.stringify(input.loaded_ids.map(String)));
            }

            if (input.category) data.append('category', input.category);
            if (input.tag) data.append('tag', input.tag);
            if (input.search) data.append('search', input.search);

            xhr.open('POST', sarai_chinwag_ajax.ajaxurl, true);
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 400) {
                    var html = xhr.responseText.trim();
                    var noContent = html === '0' || html === '' ||
                        html.indexOf('No images found') !== -1 ||
                        html.indexOf('No more images') !== -1 ||
                        html.indexOf('No posts found') !== -1 ||
                        html.indexOf('No more posts') !== -1;

                    if (noContent) {
                        resolve({ html: '', count: 0 });
                    } else {
                        var countPattern = isImageGallery
                            ? /<figure[^>]*class="[^"]*gallery-item/g
                            : /<article/g;
                        var matches = html.match(countPattern) || [];
                        resolve({ html: html, count: matches.length });
                    }
                } else {
                    reject(new Error('AJAX ' + xhr.status));
                }
            };
            xhr.onerror = function () { reject(new Error('AJAX network error')); };
            xhr.send(data);
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Render response into grid                                          */
    /* ------------------------------------------------------------------ */

    function handleResponse(html, count, append) {
        var perPage = parseInt(sarai_chinwag_ajax.posts_per_page, 10) || 10;

        if (count === 0) {
            if (append) {
                setLoadMoreState(isImageGallery ? 'No more images' : 'No more', true);
            } else {
                var contentType = isImageGallery ? 'images' : 'posts';
                SaraiGalleryUtils.getNoContentMessage(contentType).then(function (template) {
                    postGrid.innerHTML = template;
                    setLoadMoreState('', true, false);
                }).catch(function () {
                    var fallback = isImageGallery ? 'No images found.' : 'No posts found.';
                    postGrid.innerHTML = '<p class="no-content-message">' + fallback + '</p>';
                    setLoadMoreState('', true, false);
                });
            }
            return;
        }

        if (isImageGallery) {
            var tmp = document.createElement('div');
            tmp.innerHTML = html;
            var newFigs = Array.from(tmp.querySelectorAll('figure.gallery-item'));
            if (newFigs.length === 0) {
                newFigs = Array.from(tmp.querySelectorAll('figure'));
            }

            if (!append) {
                postGrid.innerHTML = '';
                var colCount = SaraiGalleryUtils.getColumnCount();
                var columns = SaraiGalleryUtils.createColumns(colCount);
                columns.forEach(function (col) { postGrid.appendChild(col); });
                galleryColumns = columns;
            }

            appendFiguresBalanced(newFigs);

            // Update loaded IDs.
            loadedImageIds = Array.from(postGrid.querySelectorAll('.gallery-item img')).map(function (img) {
                var match = img.className.match(/wp-image-(\d+)/);
                return match ? match[1] : '';
            }).filter(function (id) { return id !== ''; });
        } else {
            if (append) {
                postGrid.insertAdjacentHTML('beforeend', html);
            } else {
                postGrid.innerHTML = html;
            }

            loadedPostIds = Array.from(postGrid.querySelectorAll('article')).map(function (post) {
                return post.id.replace('post-', '');
            });
        }

        if (append) {
            currentPage++;
        } else {
            currentPage = 1;
        }

        if (count < perPage) {
            setLoadMoreState(isImageGallery ? 'No more images' : 'No more', true, true);
        } else {
            setLoadMoreState('Load More', false, true);
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Fetch dispatcher: REST primary, AJAX fallback                      */
    /* ------------------------------------------------------------------ */

    function loadContent(append) {
        if (loadMoreButton) {
            loadMoreButton.disabled = true;
            loadMoreButton.textContent = 'Loading...';
        }

        fetchViaRest(append)
            .then(function (result) {
                handleResponse(result.html, result.count, append);
            })
            .catch(function (err) {
                console.warn('REST failed, falling back to AJAX:', err);
                fetchViaAjax(append)
                    .then(function (result) {
                        handleResponse(result.html, result.count, append);
                    })
                    .catch(function (ajaxErr) {
                        console.error('Both REST and AJAX failed:', ajaxErr);
                        if (loadMoreButton) {
                            loadMoreButton.disabled = false;
                            loadMoreButton.textContent = 'Load More';
                        }
                    });
            });
    }

    /* ------------------------------------------------------------------ */
    /*  Filter controls                                                    */
    /* ------------------------------------------------------------------ */

    function applyFilters() {
        currentPage = 1;
        updateFilterStates();
        loadContent(false);
    }

    if (filterBar) {
        // Sort buttons.
        document.querySelectorAll('.sort-btn').forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                var sortValue = this.dataset.sort;
                if (currentFilters.sort_by !== sortValue) {
                    currentFilters.sort_by = sortValue;
                    applyFilters();
                }
            });
        });

        // Type buttons.
        document.querySelectorAll('.type-btn').forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                var typeValue = this.dataset.type;

                if (typeValue === 'images') {
                    navigateToImages();
                } else {
                    handleFilterOrNavigate(typeValue);
                }
            });
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Navigation helpers (image mode ↔ post mode)                        */
    /* ------------------------------------------------------------------ */

    function navigateToImages() {
        var currentUrl = window.location.pathname;
        var currentSearch = window.location.search;

        if (currentUrl.indexOf('/images') !== -1) return;

        if (currentSearch && currentSearch.indexOf('s=') !== -1) {
            window.location.href = currentUrl + 'images/' + currentSearch;
            return;
        }

        var imageUrl = (currentUrl === '/' || currentUrl.match(/^\/$/))
            ? '/images/'
            : currentUrl.replace(/\/$/, '') + '/images/';
        window.location.href = imageUrl;
    }

    function navigateFromImages() {
        var currentUrl = window.location.pathname;
        var currentSearch = window.location.search;

        if (currentUrl.indexOf('/images') === -1) return false;

        if (currentSearch && currentSearch.indexOf('s=') !== -1) {
            var postUrl = currentUrl.replace('/images/', '/').replace('/images', '/');
            window.location.href = postUrl + currentSearch;
            return true;
        }

        var postUrl2 = (currentUrl === '/images/' || currentUrl.match(/^\/images\/$/))
            ? '/'
            : currentUrl.replace('/images/', '/').replace('/images', '/');
        window.location.href = postUrl2;
        return true;
    }

    function handleFilterOrNavigate(typeValue) {
        if (navigateFromImages()) return;

        if (currentFilters.post_type_filter !== typeValue) {
            currentFilters.post_type_filter = typeValue;
            applyFilters();
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Load More button                                                   */
    /* ------------------------------------------------------------------ */

    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', function (e) {
            e.preventDefault();
            loadContent(true);
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Init                                                               */
    /* ------------------------------------------------------------------ */

    initializeLoadedContent();
    updateFilterStates();
});
