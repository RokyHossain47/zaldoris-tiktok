<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zaldoris - Live PK Battle. Watch realtime creator battles, vote, and chat live with exclusive deals.">
    <title>PK Battle - Zaldoris Live Commerce Platform</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- EMBEDDED STYLES FOR PK BATTLE SCREEN & MODALS -->
    <style>
    /* ============================================================
       PK BATTLE SCREEN PAGE STYLES (Pixel-Perfect Figma Match)
       ============================================================ */

    .pk-battle-grid {
      display: grid !important;
      grid-template-columns: 240px 1fr 340px !important;
      gap: 1.25rem !important;
      align-items: start !important;
      max-width: 1400px !important;
      margin: 0 auto !important;
      padding: 1.5rem 1rem 3.5rem !important;
    }

    .pk-battle-grid > * {
      min-width: 0 !important;
      max-width: 100% !important;
    }

    .pk-left-sidebar {
      display: flex !important;
      flex-direction: column !important;
      gap: 1.25rem !important;
    }

    .pk-host-card {
      background: #0B0F14 !important;
      border: 1px solid #16202C !important;
      border-radius: 16px !important;
      padding: 2rem 1.25rem !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      text-align: center !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }

    .pk-host-avatar-wrap {
      width: 90px !important;
      height: 90px !important;
      min-width: 90px !important;
      max-width: 90px !important;
      border-radius: 50% !important;
      border: 2px solid #00F0C8 !important;
      padding: 3px !important;
      margin-bottom: 1rem !important;
      position: relative !important;
      overflow: hidden !important;
      box-shadow: 0 0 20px rgba(0, 240, 200, 0.25) !important;
    }

    .pk-host-avatar-wrap img {
      width: 100% !important;
      height: 100% !important;
      border-radius: 50% !important;
      object-fit: cover !important;
      display: block !important;
    }

    .pk-host-name {
      font-family: 'Outfit', sans-serif !important;
      font-size: 1.25rem !important;
      font-weight: 700 !important;
      color: #FFFFFF !important;
      margin-bottom: 0.3rem !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
    }

    .badge-host-cyan {
      background: rgba(0, 240, 200, 0.15) !important;
      color: #00F0C8 !important;
      border: 1px solid rgba(0, 240, 200, 0.4) !important;
      font-size: 0.7rem !important;
      font-weight: 700 !important;
      padding: 0.15rem 0.5rem !important;
      border-radius: 20px !important;
      text-transform: lowercase !important;
    }

    .pk-host-rating {
      font-size: 0.85rem !important;
      color: #94A3B8 !important;
      margin-bottom: 0.2rem !important;
      display: flex !important;
      align-items: center !important;
      gap: 4px !important;
    }

    .pk-host-rating i {
      color: #F59E0B !important;
    }

    .pk-host-followers {
      font-size: 0.85rem !important;
      color: #94A3B8 !important;
      margin-bottom: 1.5rem !important;
    }

    .pk-host-actions {
      display: flex !important;
      flex-direction: column !important;
      gap: 0.75rem !important;
      width: 100% !important;
    }

    .btn-pk-follow {
      width: 100% !important;
      height: 42px !important;
      background: #0B0F14 !important;
      border: 1px solid #1E293B !important;
      color: #00F0C8 !important;
      font-size: 0.9rem !important;
      font-weight: 600 !important;
      border-radius: 10px !important;
      cursor: pointer !important;
      transition: all 0.3s ease !important;
    }

    .btn-pk-subscribe {
      width: 100% !important;
      height: 42px !important;
      background: #00F0C8 !important;
      color: #090D10 !important;
      font-size: 0.9rem !important;
      font-weight: 700 !important;
      border-radius: 10px !important;
      border: none !important;
      cursor: pointer !important;
      transition: all 0.3s ease !important;
    }

    .pk-middle-stream {
      background: #080C10 !important;
      border: 1px solid #16202C !important;
      border-radius: 16px !important;
      padding: 1.25rem !important;
      position: relative !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
    }

    .pk-stream-top-bar {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      margin-bottom: 1rem !important;
    }

    .pk-stream-badges {
      display: flex !important;
      align-items: center !important;
      gap: 0.5rem !important;
    }

    .badge-live-pink {
      background: #FF2A6D !important;
      color: #FFFFFF !important;
      font-size: 0.75rem !important;
      font-weight: 800 !important;
      padding: 0.3rem 0.75rem !important;
      border-radius: 20px !important;
      display: flex !important;
      align-items: center !important;
      gap: 5px !important;
    }

    .badge-viewers-dark,
    .badge-hd-dark {
      background: rgba(255, 255, 255, 0.08) !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      color: #FFFFFF !important;
      font-size: 0.78rem !important;
      font-weight: 600 !important;
      padding: 0.3rem 0.75rem !important;
      border-radius: 20px !important;
      display: flex !important;
      align-items: center !important;
      gap: 5px !important;
    }

    .pk-stream-tabs {
      display: flex !important;
      align-items: center !important;
      gap: 1.25rem !important;
      font-size: 0.88rem !important;
    }

    .pk-tab-link {
      color: #94A3B8 !important;
      text-decoration: none !important;
      font-weight: 500 !important;
      position: relative !important;
      padding-bottom: 4px !important;
    }

    .pk-tab-link.active {
      color: #FFFFFF !important;
      font-weight: 700 !important;
    }

    .pk-tab-link.active::after {
      content: '' !important;
      position: absolute !important;
      bottom: 0 !important;
      left: 0 !important;
      right: 0 !important;
      height: 2px !important;
      background: #00F0C8 !important;
      border-radius: 2px !important;
    }

    .pk-video-split-container {
      height: 600px !important;
      border-radius: 14px !important;
      overflow: hidden !important;
      display: flex !important;
      flex-direction: column !important;
      position: relative !important;
      background: #000 !important;
      border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .pk-video-frame {
      height: 286px !important;
      position: relative !important;
      overflow: hidden !important;
      flex-shrink: 0 !important;
    }

    .pk-video-frame img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      display: block !important;
    }

    .pk-vs-timer-tag {
      position: absolute !important;
      top: 1rem !important;
      left: 1rem !important;
      background: rgba(9, 13, 16, 0.75) !important;
      backdrop-filter: blur(8px) !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      border-radius: 20px !important;
      padding: 0.3rem 0.85rem !important;
      font-size: 0.78rem !important;
      font-weight: 700 !important;
      color: #FFFFFF !important;
      display: flex !important;
      align-items: center !important;
      gap: 6px !important;
      z-index: 10 !important;
    }

    .vs-pink-text {
      color: #FF2A6D !important;
      font-weight: 900 !important;
    }

    .pk-battle-score-bar {
      height: 28px !important;
      display: flex !important;
      align-items: center !important;
      position: relative !important;
      z-index: 15 !important;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.5) !important;
      flex-shrink: 0 !important;
    }

    .score-bar-cyan {
      flex: 1 !important;
      height: 100% !important;
      background: #00F0C8 !important;
      color: #090D10 !important;
      font-size: 0.82rem !important;
      font-weight: 800 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: flex-end !important;
      padding-right: 1.5rem !important;
    }

    .score-bar-pink {
      flex: 1 !important;
      height: 100% !important;
      background: #FF2A6D !important;
      color: #FFFFFF !important;
      font-size: 0.82rem !important;
      font-weight: 800 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: flex-start !important;
      padding-left: 1.5rem !important;
    }

    .score-vs-circle {
      width: 26px !important;
      height: 26px !important;
      border-radius: 50% !important;
      background: #090D10 !important;
      border: 2px solid #FF2A6D !important;
      position: absolute !important;
      left: 50% !important;
      top: 50% !important;
      transform: translate(-50%, -50%) !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      z-index: 20 !important;
    }

    .score-vs-circle::after {
      content: '' !important;
      width: 12px !important;
      height: 12px !important;
      border-radius: 50% !important;
      background: #FF2A6D !important;
    }

    .pk-caption-box {
      position: absolute !important;
      bottom: 1rem !important;
      left: 50% !important;
      transform: translateX(-50%) !important;
      background: rgba(255, 255, 255, 0.85) !important;
      backdrop-filter: blur(8px) !important;
      color: #090D10 !important;
      font-size: 0.82rem !important;
      font-weight: 600 !important;
      padding: 0.4rem 1.25rem !important;
      border-radius: 8px !important;
      white-space: nowrap !important;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
      z-index: 10 !important;
    }

    .pk-video-actions-bar {
      position: absolute !important;
      right: 1.5rem !important;
      bottom: 2.5rem !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      gap: 1.25rem !important;
      z-index: 20 !important;
    }

    .pk-action-btn-item {
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      gap: 4px !important;
    }

    .pk-circle-btn {
      width: 48px !important;
      height: 48px !important;
      border-radius: 50% !important;
      background: rgba(13, 17, 23, 0.65) !important;
      backdrop-filter: blur(10px) !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      color: #FFFFFF !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      font-size: 1.2rem !important;
      cursor: pointer !important;
      transition: all 0.3s ease !important;
    }

    .pk-action-label {
      font-size: 0.72rem !important;
      font-weight: 700 !important;
      color: #FFFFFF !important;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8) !important;
    }

    .pk-right-sidebar {
      background: #0B0F14 !important;
      border: 1px solid #16202C !important;
      border-radius: 16px !important;
      padding: 1.25rem !important;
      display: flex !important;
      flex-direction: column !important;
      height: 660px !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }

    .pk-chat-header {
      font-family: 'Outfit', sans-serif !important;
      font-size: 1.1rem !important;
      font-weight: 700 !important;
      color: #FFFFFF !important;
      display: flex !important;
      align-items: center !important;
      gap: 8px !important;
      padding-bottom: 0.85rem !important;
      border-bottom: 1px solid #16202C !important;
      margin-bottom: 1rem !important;
    }

    .chat-live-dot {
      width: 8px !important;
      height: 8px !important;
      border-radius: 50% !important;
      background: #00F0C8 !important;
      box-shadow: 0 0 8px #00F0C8 !important;
    }

    .pk-chat-messages-list {
      flex: 1 !important;
      overflow-y: auto !important;
      display: flex !important;
      flex-direction: column !important;
      gap: 1.1rem !important;
      padding-right: 4px !important;
    }

    .pk-chat-item {
      display: flex !important;
      align-items: flex-start !important;
      gap: 0.65rem !important;
    }

    .pk-chat-avatar {
      width: 32px !important;
      height: 32px !important;
      border-radius: 50% !important;
      object-fit: cover !important;
      flex-shrink: 0 !important;
    }

    .pk-chat-content {
      flex: 1 !important;
    }

    .pk-chat-user-row {
      display: flex !important;
      align-items: center !important;
      gap: 6px !important;
      margin-bottom: 4px !important;
    }

    .pk-chat-username {
      font-size: 0.8rem !important;
      font-weight: 600 !important;
      color: #94A3B8 !important;
    }

    .badge-chat-host {
      background: #00F0C8 !important;
      color: #090D10 !important;
      font-size: 0.65rem !important;
      font-weight: 800 !important;
      padding: 0.1rem 0.4rem !important;
      border-radius: 4px !important;
    }

    .pk-chat-bubble {
      background: #111822 !important;
      border: 1px solid #1E293B !important;
      border-radius: 10px !important;
      padding: 0.6rem 0.85rem !important;
      font-size: 0.83rem !important;
      color: #FFFFFF !important;
      line-height: 1.4 !important;
      display: inline-block !important;
    }

    .pk-chat-bubble.host-bubble {
      background: rgba(0, 240, 200, 0.08) !important;
      border-color: rgba(0, 240, 200, 0.4) !important;
      color: #FFFFFF !important;
    }

    .pk-chat-input-area {
      margin-top: 1rem !important;
      padding-top: 0.85rem !important;
      border-top: 1px solid #16202C !important;
    }

    .pk-input-box-wrap {
      position: relative !important;
      width: 100% !important;
      margin-bottom: 0.65rem !important;
    }

    .pk-chat-input {
      width: 100% !important;
      height: 42px !important;
      background: #0B0F14 !important;
      border: 1px solid #1E293B !important;
      border-radius: 10px !important;
      padding: 0 2.5rem 0 0.85rem !important;
      color: #FFFFFF !important;
      font-size: 0.85rem !important;
      outline: none !important;
    }

    .btn-send-chat {
      position: absolute !important;
      right: 0.5rem !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      background: transparent !important;
      border: none !important;
      color: #94A3B8 !important;
      font-size: 1rem !important;
      cursor: pointer !important;
    }

    .pk-chat-actions-bottom {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      font-size: 0.85rem !important;
    }

    .chat-extra-icons {
      display: flex !important;
      align-items: center !important;
      gap: 0.85rem !important;
      color: #94A3B8 !important;
    }

    .chat-icon-btn {
      background: transparent !important;
      border: none !important;
      color: #94A3B8 !important;
      cursor: pointer !important;
      font-size: 1.1rem !important;
      padding: 0 !important;
    }

    .gif-tag {
      font-size: 0.72rem !important;
      font-weight: 800 !important;
      background: rgba(255, 255, 255, 0.1) !important;
      padding: 0.15rem 0.4rem !important;
      border-radius: 4px !important;
    }

    .btn-send-gift {
      background: transparent !important;
      border: none !important;
      color: #94A3B8 !important;
      font-size: 0.82rem !important;
      font-weight: 600 !important;
      display: flex !important;
      align-items: center !important;
      gap: 5px !important;
      cursor: pointer !important;
    }

    /* MODALS CSS */
    .modal-overlay-backdrop {
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      width: 100vw !important;
      height: 100vh !important;
      background: rgba(9, 13, 16, 0.75) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
      z-index: 9999 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      padding: 1rem !important;
      opacity: 0 !important;
      pointer-events: none !important;
      transition: opacity 0.3s ease !important;
    }

    .modal-overlay-backdrop.show {
      opacity: 1 !important;
      pointer-events: auto !important;
    }

    .send-gift-modal-card,
    .select-package-modal-card {
      background: #0B0F14 !important;
      border: 1px solid #16202C !important;
      border-radius: 20px !important;
      width: 100% !important;
      max-width: 440px !important;
      padding: 1.5rem !important;
      position: relative !important;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 40px rgba(0, 240, 200, 0.1) !important;
      transform: scale(0.95) !important;
      transition: transform 0.3s ease !important;
      box-sizing: border-box !important;
    }

    .modal-overlay-backdrop.show .send-gift-modal-card,
    .modal-overlay-backdrop.show .select-package-modal-card {
      transform: scale(1) !important;
    }

    .modal-head-row {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      margin-bottom: 1.25rem !important;
    }

    .modal-head-title {
      font-family: 'Outfit', sans-serif !important;
      font-size: 1.35rem !important;
      font-weight: 700 !important;
      color: #FFFFFF !important;
      margin: 0 !important;
    }

    .btn-modal-close-circle {
      width: 42px !important;
      height: 42px !important;
      border-radius: 50% !important;
      background: rgba(255, 255, 255, 0.06) !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      color: #FFFFFF !important;
      font-size: 1.2rem !important;
      cursor: pointer !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      transition: all 0.3s ease !important;
    }

    .btn-modal-close-circle:hover {
      background: rgba(255, 255, 255, 0.15) !important;
      transform: rotate(90deg) !important;
    }

    /* Insufficient Coins Alert */
    .insufficient-coins-alert {
      background: rgba(239, 68, 68, 0.08) !important;
      border: 1px solid rgba(239, 68, 68, 0.25) !important;
      border-radius: 12px !important;
      padding: 0.85rem 1rem !important;
      margin-bottom: 1.25rem !important;
      display: flex !important;
      align-items: flex-start !important;
      gap: 10px !important;
    }

    .alert-warning-icon {
      color: #EF4444 !important;
      font-size: 1.15rem !important;
      flex-shrink: 0 !important;
      margin-top: 1px !important;
    }

    .alert-warning-text {
      color: #EF4444 !important;
      font-size: 0.82rem !important;
      line-height: 1.45 !important;
      margin: 0 !important;
    }

    /* Gift Items Grid */
    .gift-items-grid {
      display: grid !important;
      grid-template-columns: repeat(3, 1fr) !important;
      gap: 0.85rem !important;
      margin-bottom: 1.25rem !important;
    }

    .gift-card-item {
      background: #0E141C !important;
      border: 1px solid #1A2432 !important;
      border-radius: 14px !important;
      padding: 1.1rem 0.65rem !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      text-align: center !important;
      cursor: pointer !important;
      transition: all 0.25s ease !important;
      position: relative !important;
      box-sizing: border-box !important;
    }

    .gift-card-item:hover,
    .gift-card-item.selected {
      border-color: #00F0C8 !important;
      background: rgba(0, 240, 200, 0.06) !important;
      transform: translateY(-2px) !important;
      box-shadow: 0 4px 15px rgba(0, 240, 200, 0.15) !important;
    }

    .gift-icon-wrap {
      width: 48px !important;
      height: 48px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      margin-bottom: 0.5rem !important;
    }

    .gift-item-title {
      font-size: 0.8rem !important;
      font-weight: 600 !important;
      color: #E2E8F0 !important;
      margin-bottom: 0.4rem !important;
      white-space: nowrap !important;
    }

    .gift-price-tag {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
    }

    .cyan-z-badge {
      width: 18px !important;
      height: 18px !important;
      border-radius: 50% !important;
      background: #00F0C8 !important;
      color: #090D10 !important;
      font-weight: 900 !important;
      font-size: 0.65rem !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      font-family: 'Outfit', sans-serif !important;
      line-height: 1 !important;
    }

    .gift-price-val {
      font-family: 'Outfit', sans-serif !important;
      font-size: 1rem !important;
      font-weight: 800 !important;
      color: #FFFFFF !important;
    }

    /* Recharge Coins Button */
    .btn-recharge-coins {
      width: 100% !important;
      height: 50px !important;
      background: #00F0C8 !important;
      color: #090D10 !important;
      font-family: 'Inter', sans-serif !important;
      font-size: 1.05rem !important;
      font-weight: 800 !important;
      border-radius: 12px !important;
      border: none !important;
      cursor: pointer !important;
      transition: all 0.3s ease !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }

    .btn-recharge-coins:hover {
      background: #33F3D3 !important;
      box-shadow: 0 4px 20px rgba(0, 240, 200, 0.4) !important;
    }

    /* --- Select Package Modal --- */
    .package-cards-list {
      display: flex !important;
      flex-direction: column !important;
      gap: 1rem !important;
      margin-top: 0.5rem !important;
    }

    .package-card-item {
      background: #0E141C !important;
      border: 1px solid #1A2432 !important;
      border-radius: 14px !important;
      padding: 1.15rem 1.25rem !important;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      cursor: pointer !important;
      position: relative !important;
      transition: all 0.25s ease !important;
      overflow: hidden !important;
      box-sizing: border-box !important;
    }

    .package-card-item:hover,
    .package-card-item.selected {
      border-color: #00F0C8 !important;
      background: rgba(0, 240, 200, 0.05) !important;
      box-shadow: 0 4px 15px rgba(0, 240, 200, 0.12) !important;
    }

    .package-left-info {
      display: flex !important;
      align-items: center !important;
      gap: 1rem !important;
    }

    .package-z-icon {
      width: 44px !important;
      height: 44px !important;
      border-radius: 50% !important;
      background: #151D28 !important;
      border: 1px solid #233144 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      flex-shrink: 0 !important;
    }

    .package-coins-title {
      font-family: 'Inter', sans-serif !important;
      font-size: 1.05rem !important;
      font-weight: 700 !important;
      color: #FFFFFF !important;
      margin-bottom: 0.2rem !important;
    }

    .bonus-cyan-text {
      color: #00F0C8 !important;
      font-weight: 700 !important;
    }

    .package-pack-sub {
      font-size: 0.82rem !important;
      color: #94A3B8 !important;
      margin: 0 !important;
    }

    .package-price-tag {
      font-family: 'Outfit', sans-serif !important;
      font-size: 1.15rem !important;
      font-weight: 700 !important;
      color: #00F0C8 !important;
    }

    .badge-popular-top {
      position: absolute !important;
      top: 0 !important;
      right: 0 !important;
      background: #00F0C8 !important;
      color: #090D10 !important;
      font-size: 0.68rem !important;
      font-weight: 800 !important;
      padding: 3px 10px !important;
      border-radius: 0 14px 0 10px !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px !important;
    }
    </style>
</head>
<body>

<!-- NAVBAR / HEADER -->
<header class="zal-navbar">
    <div class="zal-navbar-inner">
        <!-- Logo -->
        <a class="zal-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="Zaldoris" class="zal-brand-logo">
        </a>

        <!-- Center Nav Links -->
        <ul class="zal-nav-menu">
            <li><a href="{{ route('home') }}" class="zal-nav-link">Home</a></li>
            <li><a href="{{ route('shop.index') }}" class="zal-nav-link">Live Shopping</a></li>
            <li><a href="{{ route('auctions.index') }}" class="zal-nav-link">Live Auction</a></li>
            <li><a href="#" class="zal-nav-link">Live Academy</a></li>
            <li><a href="{{ route('streams.index') }}" class="zal-nav-link">Live Streaming</a></li>
            <li><a href="{{ route('streams.pk_battle', 1) }}" class="zal-nav-link active">PK Battle</a></li>
        </ul>

                <!-- Right Action Icons -->
        <div class="zal-nav-actions">
            <a href="{{ route('search') }}" class="nav-icon-btn" title="Search"><i class="bi bi-search"></i></a>
            @auth
                <button class="nav-icon-btn" id="navTicketBtn" title="Wallet"><i class="bi bi-wallet2"></i></button>
                <a href="{{ route('notifications') }}" class="nav-icon-btn" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="icon-badge-dot"></span>
                </a>
                <a href="{{ route('shop.cart') }}" class="nav-icon-btn" title="Cart">
                    <i class="bi bi-cart3"></i>
                    <span class="icon-badge-num" id="globalCartBadge">2</span>
                </a>
                <a href="{{ route('dashboard.creator') }}" class="nav-avatar-btn" title="Profile">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&auto=format&fit=crop&q=80' }}" alt="{{ auth()->user()->name }}">
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-login-nav" style="background: linear-gradient(135deg, var(--cyan-accent, #00F0C8), #1ed6d0); color: #090D10; text-decoration: none; padding: 7px 18px; border-radius: 20px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; margin-left: 8px;">
                    <i class="bi bi-box-arrow-in-right"></i> Log In
                </a>
            @endauth
        </div>
    </div>
</header>

<!-- MAIN CONTAINER -->
<main class="page-container" style="max-width: 1440px; margin: 0 auto; padding: 1rem 0;">

    <!-- 3-COLUMN PK BATTLE GRID -->
    <div class="pk-battle-grid">

        <!-- LEFT COLUMN: STREAMER HOST SIDEBAR -->
        <aside class="pk-left-sidebar">
            <!-- Back Button Circle Top-Left -->
            <a href="{{ route('home') }}" class="auth-back-btn" title="Back to Home">
                <i class="bi bi-chevron-left"></i>
            </a>

            <!-- Host Card -->
            <div class="pk-host-card">
                <div class="pk-host-avatar-wrap">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80" alt="Sneaker Boss">
                </div>
                
                <h2 class="pk-host-name">
                    Sneaker Boss
                    <span class="badge-host-cyan">Host</span>
                </h2>

                <div class="pk-host-rating">
                    <i class="bi bi-star-fill"></i> 4.9 (1.2k reviews)
                </div>

                <div class="pk-host-followers">245K Followers</div>

                <div class="pk-host-actions">
                    <button class="btn-pk-follow" onclick="toggleFollow(this)">Follow</button>
                    <button class="btn-pk-subscribe">Subscribe</button>
                </div>
            </div>
        </aside>

        <!-- MIDDLE COLUMN: SPLIT LIVE PK BATTLE VIDEO SCREEN -->
        <section class="pk-middle-stream">
            <!-- Top Control Bar Overlay -->
            <div class="pk-stream-top-bar">
                <div class="pk-stream-badges">
                    <span class="badge-live-pink">● LIVE</span>
                    <span class="badge-viewers-dark"><i class="bi bi-eye"></i> 2,483 Viewers</span>
                    <span class="badge-hd-dark">HD</span>
                </div>
                <div class="pk-stream-tabs">
                    <a href="#" class="pk-tab-link">Product</a>
                    <a href="#" class="pk-tab-link active">For you</a>
                </div>
            </div>

            <!-- Split Video Frame Screen Container -->
            <div class="pk-video-split-container">
                <!-- TOP STREAMER VIDEO FRAME -->
                <div class="pk-video-frame top-frame">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&auto=format&fit=crop&q=80" alt="Streamer 1 PK Battle">
                    
                    <!-- VS Timer Tag -->
                    <div class="pk-vs-timer-tag">
                        <span class="vs-pink-text">VS</span> 02:44
                    </div>
                </div>

                <!-- BATTLE SCORE PROGRESS BAR (CYAN vs PINK) -->
                <div class="pk-battle-score-bar">
                    <div class="score-bar-cyan">
                        <span>42,800</span>
                    </div>
                    <div class="score-vs-circle"></div>
                    <div class="score-bar-pink">
                        <span>42,800</span>
                    </div>
                </div>

                <!-- BOTTOM STREAMER VIDEO FRAME -->
                <div class="pk-video-frame bottom-frame">
                    <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=800&auto=format&fit=crop&q=80" alt="Streamer 2 PK Battle">
                    
                    <!-- Bottom Subtitle Banner Overlay -->
                    <div class="pk-caption-box">
                        Calculated based on your regional jurisdiction.
                    </div>
                </div>

                <!-- RIGHT SIDE FLOATING ACTION BAR -->
                <div class="pk-video-actions-bar">
                    <div class="pk-action-btn-item">
                        <button class="pk-circle-btn" onclick="toggleLike(this)" title="Like">
                            <i class="bi bi-heart-fill" style="color: #FF2A6D;"></i>
                        </button>
                        <span class="pk-action-label">12.4K</span>
                    </div>
                    <div class="pk-action-btn-item">
                        <button class="pk-circle-btn" title="Share">
                            <i class="bi bi-share"></i>
                        </button>
                        <span class="pk-action-label">12.4K</span>
                    </div>
                    <div class="pk-action-btn-item">
                        <button class="pk-circle-btn" onclick="openSendGiftModal()" title="Send Gift">
                            <i class="bi bi-gift-fill" style="color: #00F0C8;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- RIGHT COLUMN: LIVE CHAT SIDEBAR -->
        <aside class="pk-right-sidebar">
            <div class="pk-chat-header">
                Live Chat
                <span class="chat-live-dot"></span>
            </div>

            <!-- Messages List -->
            <div class="pk-chat-messages-list" id="pkChatMessages">
                <!-- User Message 1 -->
                <div class="pk-chat-item">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&auto=format&fit=crop&q=80" class="pk-chat-avatar" alt="Fashionista92">
                    <div class="pk-chat-content">
                        <div class="pk-chat-user-row">
                            <span class="pk-chat-username">Fashionista92</span>
                        </div>
                        <div class="pk-chat-bubble">
                            Does this blouse come in extra small?
                        </div>
                    </div>
                </div>

                <!-- Host Message 2 -->
                <div class="pk-chat-item">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80" class="pk-chat-avatar" alt="Sarah Boutique">
                    <div class="pk-chat-content">
                        <div class="pk-chat-user-row">
                            <span class="pk-chat-username">Sarah Boutique</span>
                            <span class="badge-chat-host">host</span>
                        </div>
                        <div class="pk-chat-bubble host-bubble">
                            Yes! We have 5 units of XS left in stock right now!
                        </div>
                    </div>
                </div>

                <!-- User Message 3 -->
                <div class="pk-chat-item">
                    <img src="https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=100&auto=format&fit=crop&q=80" class="pk-chat-avatar" alt="AlexTrend">
                    <div class="pk-chat-content">
                        <div class="pk-chat-user-row">
                            <span class="pk-chat-username">AlexTrend</span>
                        </div>
                        <div class="pk-chat-bubble">
                            The quality looks amazing on HD!
                        </div>
                    </div>
                </div>

                <!-- User Message 4 -->
                <div class="pk-chat-item">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&auto=format&fit=crop&q=80" class="pk-chat-avatar" alt="Mila_Vibe">
                    <div class="pk-chat-content">
                        <div class="pk-chat-user-row">
                            <span class="pk-chat-username">Mila_Vibe</span>
                        </div>
                        <div class="pk-chat-bubble">
                            Just bought the tote! So excited ✨
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Input Area -->
            <div class="pk-chat-input-area">
                <form onsubmit="handleSendChat(event)" class="w-100">
                    <div class="pk-input-box-wrap">
                        <input type="text" class="pk-chat-input" id="chatInput" placeholder="Say something..." autocomplete="off">
                        <button type="submit" class="btn-send-chat" title="Send">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
                
                <div class="pk-chat-actions-bottom">
                    <div class="chat-extra-icons">
                        <button class="chat-icon-btn" title="Emoji"><i class="bi bi-emoji-smile"></i></button>
                        <button class="chat-icon-btn" title="GIF"><span class="gif-tag">GIF</span></button>
                    </div>
                    <button class="btn-send-gift" onclick="openSendGiftModal()" title="Send Gift">
                        <i class="bi bi-gift-fill" style="color: #00F0C8;"></i>
                        Send Gift
                    </button>
                </div>
            </div>
        </aside>

    </div>

</main>

<!-- SEND GIFT MODAL OVERLAY -->
<div class="modal-overlay-backdrop" id="sendGiftModal">
    <div class="send-gift-modal-card">
        <!-- Header -->
        <div class="modal-head-row">
            <h2 class="modal-head-title">Send Gift</h2>
            <button class="btn-modal-close-circle" onclick="closeSendGiftModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Warning Alert Banner -->
        <div class="insufficient-coins-alert" id="insufficientCoinsAlert">
            <i class="bi bi-exclamation-triangle-fill alert-warning-icon"></i>
            <p class="alert-warning-text">
                You don't have enough coins to send this gift. Please recharge your wallet to continue.
            </p>
        </div>

        <!-- Gift Items 3-Col Grid -->
        <div class="gift-items-grid">
            <!-- 1. Cosmic Empire -->
            <div class="gift-card-item" onclick="selectGiftItem(this, 20)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="21" cy="21" r="16" fill="url(#cosmic_grad)" opacity="0.9"/>
                        <circle cx="21" cy="21" r="19" stroke="#7033FF" stroke-width="1.5" stroke-dasharray="4 3"/>
                        <path d="M14 21L21 11L28 21L21 31L14 21Z" fill="#00F0C8" opacity="0.8"/>
                        <defs>
                            <linearGradient id="cosmic_grad" x1="5" y1="5" x2="37" y2="37" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#7033FF"/>
                                <stop offset="1" stop-color="#EC4899"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="gift-item-title">Cosmic Empire</div>
                <div class="gift-price-tag">
                    <span class="cyan-z-badge">Z</span>
                    <span class="gift-price-val">20</span>
                </div>
            </div>

            <!-- 2. Dragon Ascend -->
            <div class="gift-card-item" onclick="selectGiftItem(this, 120)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 4C14 10 10 18 13 26C15 31 20 34 25 32C30 30 33 24 30 18C28 14 24 8 21 4Z" fill="url(#dragon_grad)"/>
                        <path d="M21 10C18 15 16 20 18 25C19 28 22 30 25 29C28 28 29 24 27 20C26 18 23 13 21 10Z" fill="#F59E0B"/>
                        <defs>
                            <linearGradient id="dragon_grad" x1="10" y1="4" x2="32" y2="34" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#EF4444"/>
                                <stop offset="0.5" stop-color="#F59E0B"/>
                                <stop offset="1" stop-color="#10B981"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="gift-item-title">Dragon Ascend</div>
                <div class="gift-price-tag">
                    <span class="cyan-z-badge">Z</span>
                    <span class="gift-price-val">120</span>
                </div>
            </div>

            <!-- 3. Energy Shot -->
            <div class="gift-card-item" onclick="selectGiftItem(this, 80)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="21" cy="21" r="16" fill="url(#energy_grad)" opacity="0.3"/>
                        <path d="M23 7L13 23H22L19 35L29 19H20L23 7Z" fill="url(#energy_bolt_grad)"/>
                        <defs>
                            <linearGradient id="energy_grad" x1="5" y1="5" x2="37" y2="37" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#3B82F6"/>
                                <stop offset="1" stop-color="#00F0C8"/>
                            </linearGradient>
                            <linearGradient id="energy_bolt_grad" x1="13" y1="7" x2="29" y2="35" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#60A5FA"/>
                                <stop offset="1" stop-color="#00F0C8"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="gift-item-title">Energy Shot</div>
                <div class="gift-price-tag">
                    <span class="cyan-z-badge">Z</span>
                    <span class="gift-price-val">80</span>
                </div>
            </div>

            <!-- 4. Glow Heart -->
            <div class="gift-card-item" onclick="selectGiftItem(this, 60)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 34C21 34 33 26.5 33 17C33 12.5 29.5 9 25 9C22.3 9 19.9 10.3 18.5 12.3C17.1 10.3 14.7 9 12 9C7.5 9 4 12.5 4 17C4 26.5 16 34 16 34L21 34Z" fill="url(#heart_grad)"/>
                        <circle cx="21" cy="21" r="18" stroke="#EC4899" stroke-width="1.5" opacity="0.5"/>
                        <defs>
                            <linearGradient id="heart_grad" x1="4" y1="9" x2="33" y2="34" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#EC4899"/>
                                <stop offset="1" stop-color="#FF2A6D"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="gift-item-title">Glow Heart</div>
                <div class="gift-price-tag">
                    <span class="cyan-z-badge">Z</span>
                    <span class="gift-price-val">60</span>
                </div>
            </div>

            <!-- 5. Golden Wave -->
            <div class="gift-card-item" onclick="selectGiftItem(this, 100)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 26C14 18 21 13 29 13C24 16 20 21 21 28C22 33 28 34 32 31C26 35 18 34 14 30C12 28 11 27 10 26Z" fill="url(#wave_grad)"/>
                        <circle cx="28" cy="12" r="3" fill="#F59E0B"/>
                        <defs>
                            <linearGradient id="wave_grad" x1="10" y1="13" x2="32" y2="34" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#F59E0B"/>
                                <stop offset="1" stop-color="#EF4444"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="gift-item-title">Golden Wave</div>
                <div class="gift-price-tag">
                    <span class="cyan-z-badge">Z</span>
                    <span class="gift-price-val">100</span>
                </div>
            </div>

            <!-- 6. Royal Throne -->
            <div class="gift-card-item" onclick="selectGiftItem(this, 200)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 32V18L8 23L11 12L17 18L21 8L25 18L31 12L34 23L29 18V32H13Z" fill="url(#throne_grad)"/>
                        <rect x="11" y="32" width="20" height="4" rx="2" fill="#F59E0B"/>
                        <defs>
                            <linearGradient id="throne_grad" x1="8" y1="8" x2="34" y2="36" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#EF4444"/>
                                <stop offset="0.5" stop-color="#F59E0B"/>
                                <stop offset="1" stop-color="#D97706"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="gift-item-title">Royal Throne</div>
                <div class="gift-price-tag">
                    <span class="cyan-z-badge">Z</span>
                    <span class="gift-price-val">200</span>
                </div>
            </div>

            <!-- 7. Star Wink -->
            <div class="gift-card-item" onclick="selectGiftItem(this, 50)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 5L25.5 15.5L37 16.5L28.2 24.1L30.8 35.3L21 29.5L11.2 35.3L13.8 24.1L5 16.5L16.5 15.5L21 5Z" fill="url(#star_grad)"/>
                        <defs>
                            <linearGradient id="star_grad" x1="5" y1="5" x2="37" y2="35" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#FBBF24"/>
                                <stop offset="1" stop-color="#F59E0B"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="gift-item-title">Star Wink</div>
                <div class="gift-price-tag">
                    <span class="cyan-z-badge">Z</span>
                    <span class="gift-price-val">50</span>
                </div>
            </div>

            <!-- 8. Sonic Bloom -->
            <div class="gift-card-item" onclick="selectGiftItem(this, 140)">
                <div class="gift-icon-wrap">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M27 9V25C27 27.8 24.8 30 22 30C19.2 30 17 27.8 17 25C17 22.2 19.2 20 22 20C23.2 20 24.3 20.4 25 21.1V13L15 16V27C15 29.8 12.8 32 10 32C7.2 32 5 29.8 5 27C5 24.2 7.2 22 10 22C11.2 22 12.3 22.4 13 23.1V12L27 9Z" fill="url(#sonic_grad)"/>
                        <defs>
                            <linearGradient id="sonic_grad" x1="5" y1="9" x2="27" y2="32" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#00F0C8"/>
                                <stop offset="0.5" stop-color="#7033FF"/>
                                <stop offset="1" stop-color="#EC4899"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="gift-item-title">Sonic Bloom</div>
                <div class="gift-price-tag">
                    <span class="cyan-z-badge">Z</span>
                    <span class="gift-price-val">140</span>
                </div>
            </div>
        </div>

        <!-- Recharge Coins CTA Button -->
        <button class="btn-recharge-coins" id="giftModalCtaBtn" onclick="openSelectPackageModal()">
            Recharge Coins
        </button>
    </div>
</div>

<!-- SELECT PACKAGE MODAL OVERLAY -->
<div class="modal-overlay-backdrop" id="selectPackageModal">
    <div class="select-package-modal-card">
        <!-- Header -->
        <div class="modal-head-row">
            <h2 class="modal-head-title">Select Package</h2>
            <button class="btn-modal-close-circle" onclick="closeSelectPackageModal()" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Package List -->
        <div class="package-cards-list">
            <!-- Package 1: 100 coins -->
            <div class="package-card-item" onclick="handlePackageSelect('100 coins', 'C$0.99')">
                <div class="package-left-info">
                    <div class="package-z-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 6H18L6 18H18" stroke="#00F0C8" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="package-coins-title">100 coins</div>
                        <p class="package-pack-sub">Starter Pack</p>
                    </div>
                </div>
                <div class="package-price-tag">C$0.99</div>
            </div>

            <!-- Package 2: 500 coins -->
            <div class="package-card-item" onclick="handlePackageSelect('500 coins', 'C$4.99')">
                <div class="package-left-info">
                    <div class="package-z-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 6H18L6 18H18" stroke="#00F0C8" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="package-coins-title">500 coins</div>
                        <p class="package-pack-sub">Power User</p>
                    </div>
                </div>
                <div class="package-price-tag">C$4.99</div>
            </div>

            <!-- Package 3: 1000 coins + 100 bonus (POPULAR) -->
            <div class="package-card-item" onclick="handlePackageSelect('1000 coins', 'C$9.99')">
                <span class="badge-popular-top">POPULAR</span>
                <div class="package-left-info">
                    <div class="package-z-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 6H18L6 18H18" stroke="#00F0C8" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="package-coins-title">
                            1000 coins <span class="bonus-cyan-text">+100 bonus</span>
                        </div>
                        <p class="package-pack-sub">Pro Creator Pack</p>
                    </div>
                </div>
                <div class="package-price-tag">C$9.99</div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="zal-footer-center">
    <div class="container">
        <!-- Center Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="Zaldoris" class="footer-logo-img">
        </a>
        <p class="footer-tagline-text">
            Experience the future of shopping with realtime interaction, live demonstrations, and exclusive community deals.
        </p>
        <div class="footer-copyright-line">
            © 2024 LiveStreamShop. All rights reserved.
        </div>
    </div>
</footer>

<!-- FLOATING WIDGET BUTTON -->
<button class="floating-action-widget" title="Live Chat">
    <i class="bi bi-chat-dots-fill"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script>
    function toggleFollow(btn) {
        if (btn.innerText === 'Follow') {
            btn.innerText = 'Following';
            btn.style.background = '#00F0C8';
            btn.style.color = '#090D10';
        } else {
            btn.innerText = 'Follow';
            btn.style.background = '#0B0F14';
            btn.style.color = '#00F0C8';
        }
    }

    function toggleLike(btn) {
        const icon = btn.querySelector('i');
        if (icon.classList.contains('bi-heart-fill')) {
            icon.classList.remove('bi-heart-fill');
            icon.style.color = '#FFFFFF';
        } else {
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
            icon.style.color = '#FF2A6D';
        }
    }

    function handleSendChat(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const list = document.getElementById('pkChatMessages');
        if (input && input.value.trim() !== '') {
            const item = document.createElement('div');
            item.className = 'pk-chat-item';
            item.innerHTML = `
                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&auto=format&fit=crop&q=80" class="pk-chat-avatar" alt="You">
                <div class="pk-chat-content">
                    <div class="pk-chat-user-row">
                        <span class="pk-chat-username">You</span>
                    </div>
                    <div class="pk-chat-bubble">
                        ${input.value.trim()}
                    </div>
                </div>
            `;
            list.appendChild(item);
            input.value = '';
            list.scrollTop = list.scrollHeight;
        }
    }

    // Modal Handlers
    function openSendGiftModal() {
        const modal = document.getElementById('sendGiftModal');
        if (modal) modal.classList.add('show');
    }

    function closeSendGiftModal() {
        const modal = document.getElementById('sendGiftModal');
        if (modal) modal.classList.remove('show');
    }

    function openSelectPackageModal() {
        closeSendGiftModal();
        const modal = document.getElementById('selectPackageModal');
        if (modal) modal.classList.add('show');
    }

    function closeSelectPackageModal() {
        const modal = document.getElementById('selectPackageModal');
        if (modal) modal.classList.remove('show');
    }

    function selectGiftItem(card, price) {
        document.querySelectorAll('.gift-card-item').forEach(el => el.classList.remove('selected'));
        card.classList.add('selected');
        openSelectPackageModal();
    }

    function handlePackageSelect(packageName, price) {
        closeSelectPackageModal();
        openSendGiftModal();
        const alertBox = document.getElementById('insufficientCoinsAlert');
        if (alertBox) alertBox.style.display = 'none';
        const ctaBtn = document.getElementById('giftModalCtaBtn');
        if (ctaBtn) {
            ctaBtn.innerText = 'Send Selected Gift';
            ctaBtn.onclick = function() {
                alert('Gift sent successfully!');
                closeSendGiftModal();
            };
        }
    }
</script>
</body>
</html>
