/* Auction Page JS */
'use strict';

const allAuctions = [
  { id: 'a1', name: 'Rolex Submariner 2024', sub: 'Swiss Luxury Watch - Original Box', bid: 8450, bids: 47, time: '02:14:33', img: 'https://picsum.photos/380/285?random=70', badge: 'HOT 🔥', category: 'Luxury' },
  { id: 'a2', name: 'Nike Air Dunk Low Panda', sub: 'Size 10 US - Deadstock', bid: 320, bids: 23, time: '00:18:12', img: 'https://picsum.photos/380/285?random=71', badge: 'ENDING SOON', category: 'Sneakers' },
  { id: 'a3', name: 'Sony PS5 Pro Bundle', sub: 'Console + 3 Games + Controller', bid: 850, bids: 61, time: '04:22:08', img: 'https://picsum.photos/380/285?random=72', badge: 'POPULAR', category: 'Electronics' },
  { id: 'a4', name: 'Rare Bored Ape NFT Print', sub: 'Limited Edition Canvas Print', bid: 2100, bids: 15, time: '01:05:45', img: 'https://picsum.photos/380/285?random=73', badge: 'UNIQUE', category: 'Art' },
  { id: 'a5', name: 'Chanel Classic Flap Bag', sub: 'Black Caviar Leather - Small', bid: 4200, bids: 38, time: '03:30:00', img: 'https://picsum.photos/380/285?random=74', badge: 'LUXURY', category: 'Fashion' },
  { id: 'a6', name: 'MacBook Pro M3 Max 14"', sub: '36GB RAM, 1TB SSD - Sealed Box', bid: 2800, bids: 42, time: '05:15:22', img: 'https://picsum.photos/380/285?random=75', badge: 'NEW', category: 'Electronics' },
  { id: 'a7', name: 'Jordan 1 Retro High OG UNC', sub: 'Size 11 - 2024 Release', bid: 480, bids: 29, time: '00:52:10', img: 'https://picsum.photos/380/285?random=76', badge: 'ENDING SOON', category: 'Sneakers' },
  { id: 'a8', name: 'Cartier Love Bracelet 18K Gold', sub: 'Yellow Gold, Size 17', bid: 6300, bids: 19, time: '06:00:00', img: 'https://picsum.photos/380/285?random=77', badge: 'LUXURY', category: 'Jewelry' }
];

function formatCurrency(val) {
  return '$' + val.toLocaleString('en-US');
}

function renderFullAuctions(data) {
  const grid = document.getElementById('fullAuctionGrid');
  if (!grid) return;
  grid.innerHTML = data.map(a => `
    <div class="col-xl-3 col-lg-4 col-md-6">
      <div class="auction-card" id="${a.id}">
        <div class="auction-thumb">
          <img src="${a.img}" alt="${a.name}" loading="lazy">
          <div class="auction-top-badge">${a.badge}</div>
          <div class="auction-timer"><i class="bi bi-clock-fill"></i> <span class="countdown" data-time="${a.time}">${a.time}</span></div>
        </div>
        <div class="auction-body">
          <div class="auction-product-name">${a.name}</div>
          <div class="auction-product-sub">${a.sub}</div>
          <div class="bid-row">
            <div><div class="bid-label">Current Bid</div><div class="bid-amount" id="bid-${a.id}">${formatCurrency(a.bid)}</div></div>
            <div class="text-end"><div class="bid-label">Bids</div><div class="bid-count" id="count-${a.id}">${a.bids}</div></div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn-bid flex-grow-1" onclick="quickBid('${a.id}', ${a.bid})"><i class="bi bi-hammer me-1"></i>Bid Now</button>
            <button class="btn-watch-sm" onclick="watchAuction('${a.id}', this)" title="Watch"><i class="bi bi-eye"></i></button>
          </div>
        </div>
      </div>
    </div>
  `).join('');
  startCountdowns();
}

function quickBid(id, currentBid) {
  const increment = currentBid < 500 ? 10 : currentBid < 2000 ? 50 : currentBid < 10000 ? 100 : 500;
  const newBid = currentBid + increment;
  const bidEl = document.getElementById('bid-' + id);
  const countEl = document.getElementById('count-' + id);
  if (bidEl) {
    bidEl.textContent = formatCurrency(newBid);
    bidEl.style.animation = 'none';
    bidEl.offsetHeight;
    bidEl.style.animation = 'bidFlash 0.5s ease';
  }
  if (countEl) countEl.textContent = parseInt(countEl.textContent) + 1;
  allAuctions.find(a => a.id === id).bid = newBid;
}

function watchAuction(id, btn) {
  const icon = btn.querySelector('i');
  if (icon.classList.contains('bi-eye-fill')) {
    icon.classList.replace('bi-eye-fill', 'bi-eye');
    btn.style.color = '';
  } else {
    icon.classList.replace('bi-eye', 'bi-eye-fill');
    btn.style.color = 'var(--accent)';
  }
}

// Btn watch sm style
const style = document.createElement('style');
style.textContent = `
  .btn-watch-sm {
    background: rgba(255,255,255,0.07);
    border: 1px solid var(--border-subtle);
    color: var(--text-secondary);
    width: 40px; height: 40px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: var(--transition);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
  }
  .btn-watch-sm:hover { border-color: var(--border-accent); color: var(--accent); }
  @keyframes bidFlash {
    0% { color: var(--text-primary); }
    50% { color: var(--accent); transform: scale(1.1); }
    100% { color: var(--warning); }
  }
`;
document.head.appendChild(style);

function placeFeaturedBid() {
  const input = document.getElementById('heroCustomBid');
  const amtEl = document.getElementById('heroBidAmt');
  const cntEl = document.getElementById('heroBidCount');
  if (!input || !amtEl) return;
  const val = parseFloat(input.value);
  if (isNaN(val) || val < 48300) {
    input.style.border = '1px solid var(--danger)';
    setTimeout(() => input.style.border = '', 1500);
    return;
  }
  amtEl.textContent = formatCurrency(val);
  if (cntEl) cntEl.textContent = parseInt(cntEl.textContent) + 1;
  input.value = val + 100;
  const btn = document.getElementById('heroBidBtn');
  if (btn) {
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Bid Placed!';
    btn.style.background = 'linear-gradient(135deg,var(--accent),var(--accent-blue))';
    btn.style.color = 'var(--bg-primary)';
    setTimeout(() => {
      btn.innerHTML = '<i class="bi bi-hammer me-2"></i>Place Bid';
      btn.style.background = '';
      btn.style.color = '';
    }, 2500);
  }
}

function initFilterBtns() {
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const id = btn.id;
      let filtered = allAuctions;
      if (id === 'filtEndingSoon') filtered = [...allAuctions].sort((a, b) => {
        const toSec = t => t.split(':').reduce((acc, v) => acc * 60 + parseInt(v), 0);
        return toSec(a.time) - toSec(b.time);
      });
      else if (id === 'filtLowBid') filtered = [...allAuctions].sort((a, b) => a.bid - b.bid);
      else if (id === 'filtLuxury') filtered = allAuctions.filter(a => a.category === 'Luxury' || a.category === 'Jewelry' || a.category === 'Fashion');
      renderFullAuctions(filtered);
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  renderFullAuctions(allAuctions);
  initFilterBtns();

  // Navbar scroll
  const nav = document.getElementById('mainNav');
  window.addEventListener('scroll', () => {
    nav?.classList.toggle('scrolled', window.scrollY > 60);
  });

  // Search
  document.getElementById('searchBtn')?.addEventListener('click', () => {
    document.getElementById('searchOverlay')?.classList.add('active');
    document.getElementById('searchInput')?.focus();
  });
  document.getElementById('searchClose')?.addEventListener('click', () => {
    document.getElementById('searchOverlay')?.classList.remove('active');
  });

  // Back to top
  const btt = document.getElementById('backToTop');
  window.addEventListener('scroll', () => btt?.classList.toggle('show', window.scrollY > 500));
  btt?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  // Hero follow seller
  document.getElementById('heroFollowSeller')?.addEventListener('click', function() {
    this.textContent = this.textContent === 'Follow Seller' ? '✓ Following' : 'Follow Seller';
    this.style.background = this.textContent === '✓ Following' ? 'var(--accent)' : '';
    this.style.color = this.textContent === '✓ Following' ? 'var(--bg-primary)' : '';
  });
});
