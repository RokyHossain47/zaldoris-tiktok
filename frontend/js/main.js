/* ============================================================
   ZALDORIS - Main JavaScript
   ============================================================ */

'use strict';

// ---- COUNTDOWN TIMERS ----
function startCountdowns() {
  const elements = document.querySelectorAll('.countdown');
  elements.forEach(el => {
    const parseTime = (t) => {
      const parts = t.split(':').map(Number);
      if (parts.length === 3) return parts[0] * 3600 + parts[1] * 60 + parts[2];
      return 8095;
    };
    const formatTime = (s) => {
      const h = Math.floor(s / 3600);
      const m = Math.floor((s % 3600) / 60);
      const sec = s % 60;
      return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
    };
    let remaining = parseTime(el.dataset.time || el.textContent);
    const interval = setInterval(() => {
      remaining--;
      if (remaining <= 0) {
        clearInterval(interval);
        el.textContent = 'ENDED';
        el.style.color = 'var(--danger)';
        return;
      }
      el.textContent = formatTime(remaining);
    }, 1000);
  });
}

// ---- INTERACTION HANDLERS ----

// Add item to cart
function addCartItem(productName) {
  const badge = document.getElementById('globalCartBadge');
  if (badge) {
    let curr = parseInt(badge.textContent || '0');
    badge.textContent = curr + 1;
    badge.style.transform = 'scale(1.3)';
    setTimeout(() => badge.style.transform = 'scale(1)', 200);
  }
}

// Place Bid Handler
function handlePlaceBid(btn, itemTitle) {
  const card = btn.closest('.zal-card');
  const bidValEl = card ? card.querySelector('.auction-bid-val') : null;
  if (bidValEl) {
    let raw = bidValEl.textContent.replace(/[^0-9]/g, '');
    let num = parseInt(raw) || 2850;
    num += 50;
    bidValEl.textContent = 'C$' + num.toLocaleString();
    bidValEl.style.color = 'var(--accent)';
    setTimeout(() => bidValEl.style.color = '', 1000);
  }
  btn.textContent = '✓ Bid Placed!';
  btn.style.background = '#00D8B4';
  setTimeout(() => {
    btn.textContent = 'Place Bid';
    btn.style.background = '';
  }, 2000);
}

// Creator Follow Toggle
function toggleCreatorFollow(btn) {
  if (btn.classList.contains('following')) {
    btn.classList.remove('following');
    btn.textContent = 'Follow';
  } else {
    btn.classList.add('following');
    btn.textContent = 'Following';
  }
}

// Event Reminder Toggle
function toggleEventBell(icon) {
  if (icon.classList.contains('bi-bell-fill')) {
    icon.classList.replace('bi-bell-fill', 'bi-bell');
    icon.style.color = '';
  } else {
    icon.classList.replace('bi-bell', 'bi-bell-fill');
    icon.style.color = 'var(--accent)';
  }
}

// Rewards Claim Handler
function initRewardsClaim() {
  const claimBtn = document.getElementById('btnClaimRewards');
  if (claimBtn) {
    claimBtn.addEventListener('click', function() {
      this.textContent = '✓ Claimed!';
      this.disabled = true;
      this.style.opacity = '0.8';
      const balEl = document.querySelector('.rewards-balance-text');
      if (balEl) {
        balEl.textContent = 'Balance 🪙 1,950 (+500)';
      }
    });
  }
}

// Category Arrow Scroll
function initCategoryArrows() {
  const prevBtn = document.getElementById('catPrevBtn');
  const nextBtn = document.getElementById('catNextBtn');
  const container = document.querySelector('.categories-scroll-row');
  if (prevBtn && container) {
    prevBtn.addEventListener('click', () => container.scrollBy({ left: -200, behavior: 'smooth' }));
  }
  if (nextBtn && container) {
    nextBtn.addEventListener('click', () => container.scrollBy({ left: 200, behavior: 'smooth' }));
  }
}

// Refresh Creators
function initRefreshCreators() {
  const btn = document.getElementById('btnRefreshCreators');
  if (btn) {
    btn.addEventListener('click', () => {
      btn.style.transform = 'rotate(180deg)';
      btn.style.transition = 'transform 0.5s ease';
      setTimeout(() => btn.style.transform = '', 500);
    });
  }
}

// ---- DOM LOAD ----
document.addEventListener('DOMContentLoaded', () => {
  startCountdowns();
  initRewardsClaim();
  initCategoryArrows();
  initRefreshCreators();
});
