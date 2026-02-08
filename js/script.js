// ===== DOM Elements =====
const landingScreen = document.getElementById('landingScreen');
const mainMenu = document.getElementById('mainMenu');
const gallerySection = document.getElementById('gallerySection');
const messagesSection = document.getElementById('messagesSection');
const timelineSection = document.getElementById('timelineSection');
const loveReasonsSection = document.getElementById('loveReasonsSection');
const gamesSection = document.getElementById('gamesSection');

const unlockBtn = document.getElementById('unlockBtn');
const particlesContainer = document.getElementById('particles');
const galleryGrid = document.getElementById('galleryGrid');
const addPhotoBtn = document.getElementById('addPhotoBtn');
const photoInput = document.getElementById('photoInput');
const uploadStatus = document.getElementById('uploadStatus');
const addMessageForm = document.getElementById('addMessageForm');
const messageStatus = document.getElementById('messageStatus');
const msgText = document.getElementById('msgText');
const charCount = document.getElementById('charCount');
const selectAllBtn = document.getElementById('selectAllBtn');
const selectionCount = document.getElementById('selectionCount');
const selectedFiles = new Set();

// ===== Navigation ===== 
const menuCards = document.querySelectorAll('.menu-card');
const backButtons = document.querySelectorAll('.back-btn');

// ===== Event Listeners =====
unlockBtn.addEventListener('click', unlockSurprise);

// Real-time character counter for message textarea
if (msgText && charCount) {
    msgText.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
}

menuCards.forEach(card => {
    card.addEventListener('click', handleMenuCardClick);
});

// Upload button handlers
if (addPhotoBtn && photoInput) {
    addPhotoBtn.addEventListener('click', () => photoInput.click());
    photoInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        uploadStatus.textContent = 'Uploading...';

        const form = new FormData();
        form.append('media', file);

        fetch('php/upload_media.php', { method: 'POST', body: form })
            .then(res => res.json().catch(() => ({ success: false, error: 'Invalid JSON response' })))
            .then(data => {
                console.log('upload response', data);
                if (data && data.success) {
                    uploadStatus.textContent = 'Uploaded! Refreshing...';
                    setTimeout(() => { uploadStatus.textContent = ''; loadGallery(); }, 800);
                } else {
                    const errMsg = data && (data.error || (data.debug && JSON.stringify(data.debug))) ? (data.error || JSON.stringify(data.debug)) : 'Upload failed';
                    uploadStatus.textContent = errMsg;
                    console.error('Upload error:', data);
                }
            })
            .catch(err => { console.error('Fetch error', err); uploadStatus.textContent = 'Upload failed (network)'; });
    });
}

// selection controls
if (selectAllBtn) selectAllBtn.addEventListener('click', selectAll);

backButtons.forEach(btn => {
    btn.addEventListener('click', handleBackClick);
});

// ===== Unlock Surprise =====
function unlockSurprise() {
    // Create particle explosion
    createParticleExplosion();
    
    // Add celebration animation
    unlockBtn.style.transform = 'scale(0.95)';
    
    // Hide landing screen and show menu
    setTimeout(() => {
        landingScreen.style.display = 'none';
        mainMenu.style.display = 'block';
        unlockBtn.style.transform = 'scale(1)';
    }, 600);
}

// ===== Particle Creation =====
function createParticleExplosion() {
    const particles = ['❤️', '💕', '💖', '💗', '✨', '💝', '🌹'];
    const particleCount = 30;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.textContent = particles[Math.floor(Math.random() * particles.length)];
        
        const x = Math.random() * 200 - 100;
        particle.style.setProperty('--tx', x + 'px');
        
        particlesContainer.appendChild(particle);
        
        // Remove particle after animation
        setTimeout(() => {
            particle.remove();
        }, 2000);
    }
}

// ===== Menu Navigation =====
function handleMenuCardClick(e) {
    const clickedCard = e.currentTarget;
    const cardId = clickedCard.id;
    
    // Hide all sections
    hideAllSections();
    
    // Show relevant section
    if (cardId === 'galleryCard') {
        gallerySection.style.display = 'block';
        loadGallery();
    } else if (cardId === 'messagesCard') {
        messagesSection.style.display = 'block';
        loadMessages();
    } else if (cardId === 'timelineCard') {
        timelineSection.style.display = 'block';
        startTimelineRotation();
    } else if (cardId === 'loveCard') {
        loveReasonsSection.style.display = 'block';
    } else if (cardId === 'gamesCard') {
        gamesSection.style.display = 'block';
    }
}

function handleBackClick(e) {
    e.preventDefault();
    hideAllSections();
    mainMenu.style.display = 'block';
}

function hideAllSections() {
    gallerySection.style.display = 'none';
    messagesSection.style.display = 'none';
    timelineSection.style.display = 'none';
    loveReasonsSection.style.display = 'none';
    gamesSection.style.display = 'none';
    mainMenu.style.display = 'none';
}

// ===== Gallery Loading =====
function loadGallery() {
    // Clear existing gallery items (keep placeholder)
    const galleryItems = galleryGrid.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => item.remove());

    // Update message
    const note = document.querySelector('#gallerySection .gallery-note');
    if (note) note.textContent = 'Loading your memories... 📸🎥';
    
    // Fetch media files from PHP helper
    fetch('php/get_media.php')
        .then(response => response.json())
        .then(mediaFiles => {
            if (!mediaFiles || mediaFiles.length === 0) {
                if (note) {
                    note.textContent = 'No photos or videos found. Add some to the img/ folder! 📸🎥';
                }
                return;
            }
            
            // Update note with count
            if (note) note.textContent = `Found ${mediaFiles.length} memories! 📸🎥`;
            
            // Build elements in a fragment then append once to minimize reflows
            const fragment = document.createDocumentFragment();
            mediaFiles.forEach((media) => {
                let el;
                if (media.type === 'image') {
                    el = createImageItem(media.path);
                } else if (media.type === 'video') {
                    el = createVideoItem(media.path);
                }
                if (el) fragment.appendChild(el);
            });

            // Remove existing gallery items and append new in one operation
            const existing = galleryGrid.querySelectorAll('.gallery-item');
            existing.forEach(i => i.remove());
            galleryGrid.appendChild(fragment);

            // Observe media for lazy-loading
            observeMediaItems();
        })
        .catch(error => {
            console.log('Could not load media automatically. Using fallback...');
            console.error('Error:', error);
            
            // Fallback: Load hardcoded files if PHP fails
            const fallbackMedia = [
                { path: 'img/a.mp4', type: 'video' },
                { path: 'img/b.mp4', type: 'video' }
            ];
            
            fallbackMedia.forEach((media) => {
                const testElement = media.type === 'image' ? new Image() : document.createElement('video');
                testElement.onload = () => addImageToGallery(media.path);
                testElement.onloadedmetadata = () => addVideoToGallery(media.path);
                testElement.src = media.path;
            });
        });
}

function addImageToGallery(imagePath) {
    // kept for backward compatibility; prefer createImageItem
    const item = createImageItem(imagePath);
    galleryGrid.appendChild(item);
}

function addVideoToGallery(videoPath) {
    // kept for backward compatibility; prefer createVideoItem
    const item = createVideoItem(videoPath);
    galleryGrid.appendChild(item);
}

// Create a gallery item for an image with lazy loading (data-src)
function createImageItem(path) {
    const galleryItem = document.createElement('div');
    galleryItem.className = 'gallery-item';
    galleryItem.dataset.type = 'image';

    // allow positioned overlay controls
    galleryItem.style.position = 'relative';

    const img = document.createElement('img');
    img.alt = 'Memory';
    img.dataset.src = path; // actual source will be set when visible
    img.loading = 'lazy';
    img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='; // tiny placeholder

    const filename = path.split('/').pop();
    galleryItem.dataset.filename = filename;

    galleryItem.addEventListener('click', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('delete-btn')) return;
        toggleSelection(this);
    });

    galleryItem.appendChild(img);
    // delete button overlay
    const del = document.createElement('button');
    del.className = 'delete-btn';
    del.textContent = '🗑';
    del.title = 'Delete photo';
    Object.assign(del.style, { position: 'absolute', top: '6px', right: '6px', background: 'rgba(0,0,0,0.6)', color: '#fff', border: 'none', padding: '6px', borderRadius: '6px', cursor: 'pointer' });
    del.addEventListener('click', function(e){ e.stopPropagation(); confirmAndDelete(filename, galleryItem); });
    galleryItem.appendChild(del);
    return galleryItem;
}

// Create a gallery item for a video with deferred loading
function createVideoItem(path) {
    const galleryItem = document.createElement('div');
    galleryItem.className = 'gallery-item gallery-video';
    galleryItem.dataset.type = 'video';

    galleryItem.style.position = 'relative';

    const video = document.createElement('video');
    video.controls = true;
    video.preload = 'none';
    video.muted = true;
    video.playsInline = true;

    const source = document.createElement('source');
    source.dataset.src = path; // set when visible

    video.appendChild(source);
    video.innerHTML += 'Your browser does not support HTML5 video.';

    galleryItem.appendChild(video);
    const filename = path.split('/').pop();
    galleryItem.dataset.filename = filename;
    galleryItem.addEventListener('click', function(e){
        if (e.target && e.target.classList && e.target.classList.contains('delete-btn')) return;
        toggleSelection(this);
    });

    const del = document.createElement('button');
    del.className = 'delete-btn';
    del.textContent = '🗑';
    del.title = 'Delete video';
    Object.assign(del.style, { position: 'absolute', top: '6px', right: '6px', background: 'rgba(0,0,0,0.6)', color: '#fff', border: 'none', padding: '6px', borderRadius: '6px', cursor: 'pointer' });
    del.addEventListener('click', function(e){ e.stopPropagation(); confirmAndDelete(filename, galleryItem); });
    galleryItem.appendChild(del);
    return galleryItem;
}

function confirmAndDelete(filename, itemEl) {
    if (!confirm('Delete this file? This cannot be undone.')) return;
    // optimistically remove
    const statusEl = document.getElementById('uploadStatus');
    if (statusEl) statusEl.textContent = 'Deleting...';
    fetch('php/delete_media.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: filename })
    })
    .then(r => r.json().catch(()=>({ success:false, error:'Invalid response' })))
    .then(data => {
        if (data && data.success) {
            if (itemEl && itemEl.parentNode) itemEl.parentNode.removeChild(itemEl);
            if (statusEl) statusEl.textContent = 'Deleted';
            setTimeout(()=>{ if (statusEl) statusEl.textContent = ''; }, 900);
        } else {
            console.error('Delete failed', data);
            if (statusEl) statusEl.textContent = data.error || 'Delete failed';
        }
    })
    .catch(err => { console.error(err); if (statusEl) statusEl.textContent = 'Delete failed (network)'; });
}

function toggleSelection(itemEl) {
    const fn = itemEl.dataset.filename;
    if (!fn) return;
    if (selectedFiles.has(fn)) {
        selectedFiles.delete(fn);
        itemEl.style.boxShadow = '';
        itemEl.style.transform = '';
        itemEl.classList.remove('selected');
    } else {
        selectedFiles.add(fn);
        itemEl.style.boxShadow = '0 6px 18px rgba(0,0,0,0.12)';
        itemEl.style.transform = 'scale(0.98)';
        itemEl.classList.add('selected');
    }
    updateSelectionCount();
}

function updateSelectionCount() {
    if (!selectionCount) return;
    const n = selectedFiles.size;
    selectionCount.textContent = n ? `${n} selected` : '';
}

function selectAll() {
    const items = galleryGrid.querySelectorAll('.gallery-item');
    items.forEach(i => {
        const fn = i.dataset.filename;
        if (!fn) return;
        selectedFiles.add(fn);
        i.classList.add('selected');
        i.style.boxShadow = '0 6px 18px rgba(0,0,0,0.12)';
        i.style.transform = 'scale(0.98)';
    });
    updateSelectionCount();
}

// Bulk delete removed: UI no longer exposes a 'Delete Selected' button

// IntersectionObserver to lazy-load images and videos when they approach viewport
let _mediaObserver = null;
function observeMediaItems() {
    if (!_mediaObserver) {
        _mediaObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const type = el.dataset.type;
                if (type === 'image') {
                    const img = el.querySelector('img');
                    if (img && img.dataset.src) {
                        img.src = img.dataset.src;
                        delete img.dataset.src;
                        // attempt to decode to avoid layout jank
                        if (img.decode) img.decode().catch(()=>{});
                    }
                } else if (type === 'video') {
                    const source = el.querySelector('source');
                    const video = el.querySelector('video');
                    if (source && source.dataset.src) {
                        source.src = source.dataset.src;
                        delete source.dataset.src;
                        // load metadata when about to play
                        try { video.load(); } catch(e) {}
                    }
                }
                _mediaObserver.unobserve(el);
            });
        }, { rootMargin: '300px' });
    }

    const items = galleryGrid.querySelectorAll('.gallery-item');
    items.forEach(i => _mediaObserver.observe(i));
}

// ===== Add Glow Effect to Heart Emojis =====
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideAllSections();
            mainMenu.style.display = 'block';
        }
    });
    
    // Add hover effect to heart animations
    const hearts = document.querySelectorAll('.heart');
    hearts.forEach(heart => {
        heart.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.3) rotate(15deg)';
        });
        heart.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});

// Message form submit handler
if (addMessageForm) {
    addMessageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const sender = document.getElementById('msgSender').value || 'Anonymous';
        const message = document.getElementById('msgText').value || '';
        if (!message.trim()) {
            messageStatus.textContent = '❌ Please enter a message';
            messageStatus.style.color = '#e74c3c';
            setTimeout(() => { messageStatus.textContent = ''; }, 2000);
            return;
        }
        messageStatus.textContent = '✨ Sending...';
        messageStatus.style.color = '#feca57';

        fetch('php/messages.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sender: sender, message: message })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                messageStatus.textContent = '💕 Message saved!';
                messageStatus.style.color = '#48a9a6';
                document.getElementById('msgSender').value = '';
                document.getElementById('msgText').value = '';
                if (charCount) charCount.textContent = '0';
                setTimeout(() => { messageStatus.textContent = ''; }, 1500);
                // refresh messages
                loadMessages();
            } else {
                messageStatus.textContent = '❌ ' + (data.error || 'Failed to save');
                messageStatus.style.color = '#e74c3c';
            }
        })
        .catch(err => { 
            console.error(err); 
            messageStatus.textContent = '❌ Failed to save'; 
            messageStatus.style.color = '#e74c3c';
        });
    });
}

// Fetch and render messages
function loadMessages() {
    const list = document.getElementById('messagesList');
    if (!list) return;
    list.innerHTML = '<p style="color:#777;padding:12px">Loading messages...</p>';
    fetch('php/messages.php')
        .then(r => r.json())
        .then(data => {
            if (!data || !data.success) {
                list.innerHTML = '<p style="color:#c00;padding:12px">Could not load messages.</p>';
                return;
            }
            const messages = data.messages || [];
            if (messages.length === 0) {
                list.innerHTML = '<p style="color:#777;padding:12px">No messages yet. Be the first to write one 💌</p>';
                return;
            }

            const frag = document.createDocumentFragment();
            messages.forEach(msg => {
                const card = document.createElement('div');
                card.className = 'message-card dynamic';
                card.dataset.id = msg.id;

                const header = document.createElement('div');
                header.className = 'message-header';
                header.innerHTML = `<span class="message-emoji">${msg.emoji || '💌'}</span><h3>${escapeHtml(msg.sender || 'Anonymous')}</h3>`;

                const p = document.createElement('p');
                p.className = 'message-text';
                p.textContent = msg.message || '';

                const footer = document.createElement('div');
                footer.style.display = 'flex';
                footer.style.justifyContent = 'space-between';
                footer.style.alignItems = 'center';

                const sign = document.createElement('p');
                sign.className = 'message-signature';
                sign.textContent = formatDate(msg.created_at || msg.timestamp || '');

                const actions = document.createElement('div');
                actions.className = 'msg-actions';

                // like button (client-side)
                const likeBtn = document.createElement('button');
                likeBtn.className = 'btn-like';
                const likesKey = 'likes_' + (msg.id || '');
                const liked = !!localStorage.getItem(likesKey);
                likeBtn.textContent = (liked ? '💖' : '🤍') + ' Like';
                likeBtn.addEventListener('click', () => {
                    if (localStorage.getItem(likesKey)) {
                        localStorage.removeItem(likesKey);
                        likeBtn.textContent = '🤍 Like';
                    } else {
                        localStorage.setItem(likesKey, '1');
                        likeBtn.textContent = '💖 Like';
                    }
                });

                // delete button (calls messages.php DELETE)
                const del = document.createElement('button');
                del.className = 'btn btn-danger';
                del.textContent = 'Delete';
                del.addEventListener('click', () => {
                    if (!confirm('Delete this message?')) return;
                    fetch('php/messages.php', { method: 'DELETE', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ id: msg.id }) })
                        .then(r => r.json())
                        .then(d => {
                            if (d && d.success) {
                                loadMessages();
                            } else {
                                alert(d.error || 'Delete failed');
                            }
                        })
                        .catch(e => { console.error(e); alert('Delete failed'); });
                });

                actions.appendChild(likeBtn);
                actions.appendChild(del);

                footer.appendChild(sign);
                footer.appendChild(actions);

                card.appendChild(header);
                card.appendChild(p);
                card.appendChild(footer);

                frag.appendChild(card);
            });

            list.innerHTML = '';
            list.appendChild(frag);
        })
        .catch(err => { console.error(err); list.innerHTML = '<p style="color:#c00;padding:12px">Could not load messages.</p>'; });
}

function escapeHtml(s){
    return String(s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; });
}

function formatDate(ts) {
    if (!ts) return '';
    // try to parse and format nicely
    const d = new Date(ts);
    if (isNaN(d)) return ts;
    return d.toLocaleString();
}

// ===== Confetti on Page Load =====
window.addEventListener('load', function() {
    // Optional: Create confetti animation on page load
    if (Math.random() > 0.8) {
        createParticleExplosion();
    }
});

// ===== Smooth Transitions =====
function showSection(section) {
    section.style.animation = 'slideUp 0.6s ease-out';
    section.style.display = 'block';
}

// ===== Message Card Interactivity =====
document.addEventListener('DOMContentLoaded', function() {
    const messageCards = document.querySelectorAll('.message-card');
    messageCards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(1.02)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });
    
    // Timeline interactivity
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item, index) => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(' + (index % 2 === 0 ? '10' : '-10') + 'px)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // Reason card interactivity
    const reasonCards = document.querySelectorAll('.reason-card');
    reasonCards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(1.05) rotateZ(2deg)';
            setTimeout(() => {
                this.style.transform = 'scale(1) rotateZ(0)';
            }, 300);
        });
    });
});

// ===== Background Music Control (Optional) =====
// You can add a background music player here if desired
// Example:
/*
window.addEventListener('load', function() {
    const audio = document.createElement('audio');
    audio.src = 'music/background.mp3';
    audio.loop = true;
    audio.volume = 0.3;
    // Note: Autoplay is restricted in most browsers
    // User must interact first
});
*/

// ===== Love Counter Animation =====
document.addEventListener('DOMContentLoaded', function() {
    // Add 'You Mean Everything' count animation
    const reasonNumbers = document.querySelectorAll('.reason-number');
    reasonNumbers.forEach(number => {
        number.addEventListener('click', function() {
            this.style.animation = 'pulse 0.6s ease-out';
            setTimeout(() => {
                this.style.animation = '';
            }, 600);
        });
    });
});

// ===== Easter Egg - Type a secret message =====
let secretCode = 'ILOVEYOU';
let secretInput = '';

document.addEventListener('keydown', function(e) {
    secretInput += e.key.toUpperCase();
    if (secretInput.length > secretCode.length) {
        secretInput = secretInput.substring(secretInput.length - secretCode.length);
    }
    
    if (secretInput === secretCode) {
        createParticleExplosion();
        secretInput = '';
    }
});

// ===== Timeline Auto-Rotation (Every 5 seconds) =====
let timelineRotationInterval = null;
let currentTimelineIndex = 0;

function startTimelineRotation() {
    // Clear any existing interval
    if (timelineRotationInterval) clearInterval(timelineRotationInterval);
    
    const items = document.querySelectorAll('.timeline-item');
    if (items.length === 0) return;
    
    // Reset all items
    items.forEach(item => {
        item.classList.remove('active');
        item.style.opacity = '0.5';
        item.style.transform = 'scale(0.95)';
    });
    
    currentTimelineIndex = 0;
    
    // Show first item
    highlightTimelineItem(0);
    
    // Auto-rotate every 5 seconds
    timelineRotationInterval = setInterval(() => {
        currentTimelineIndex = (currentTimelineIndex + 1) % items.length;
        highlightTimelineItem(currentTimelineIndex);
    }, 5000);
}

function highlightTimelineItem(index) {
    const items = document.querySelectorAll('.timeline-item');
    items.forEach((item, i) => {
        if (i === index) {
            item.classList.add('active');
            item.style.opacity = '1';
            item.style.transform = 'scale(1)';
            item.style.transition = 'all 0.6s ease-out';
        } else {
            item.classList.remove('active');
            item.style.opacity = '0.5';
            item.style.transform = 'scale(0.95)';
            item.style.transition = 'all 0.3s ease';
        }
    });
}

// Stop timeline rotation when going back
backButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        if (timelineRotationInterval) {
            clearInterval(timelineRotationInterval);
            timelineRotationInterval = null;
        }
    });
});

