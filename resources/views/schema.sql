CREATE DATABASE IF NOT EXISTS davao_boss_computer;
USE davao_boss_computer;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(160) NOT NULL UNIQUE,
    type ENUM('desktop', 'laptop', 'accessory') NOT NULL,
    category VARCHAR(50) NOT NULL,
    price INT UNSIGNED NOT NULL,
    size VARCHAR(20) DEFAULT NULL,
    tags VARCHAR(255) DEFAULT NULL, -- comma-separated, e.g. "itx,1080p"
    image_path VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO products (name, slug, type, category, price, size, tags) VALUES
-- Desktops
('Boss Strike ITX',     'boss-strike-itx',     'desktop', 'ready-to-ship', 54999,  NULL, 'itx,1080p'),
('Boss Vanguard Mid',   'boss-vanguard-mid',   'desktop', 'ready-to-ship', 74999,  NULL, 'mid,1440p'),
('Boss Apex 4K',        'boss-apex-4k',        'desktop', 'gaming',        119999, NULL, 'full,4k'),
('Boss Workstation X',  'boss-workstation-x',  'desktop', 'workstation',   149999, NULL, 'full,4k'),
('Boss Strike Mini',    'boss-strike-mini',    'desktop', 'gaming',        47999,  NULL, 'itx,1080p'),
('Boss Vanguard Pro',   'boss-vanguard-pro',   'desktop', 'workstation',   99999,  NULL, 'mid,1440p'),

-- Laptops
('Boss Nomad 14',       'boss-nomad-14',       'laptop', 'thin-and-light', 64999,  '14"',    NULL),
('Boss Nomad 15',       'boss-nomad-15',       'laptop', 'thin-and-light', 69999,  '15.6"',  NULL),
('Boss Overclock 16',   'boss-overclock-16',   'laptop', 'performance',    94999,  '16"',    NULL),
('Boss Overclock 17',   'boss-overclock-17',   'laptop', 'performance',    109999, '17.3"',  NULL),

-- Accessories
('Boss Mechanical Keyboard', 'boss-mechanical-keyboard', 'accessory', 'peripherals', 3499,  NULL, NULL),
('Boss Wireless Mouse',      'boss-wireless-mouse',      'accessory', 'peripherals', 2299,  NULL, NULL),
('27" 1440p Monitor',        '27-1440p-monitor',         'accessory', 'displays',    12999, NULL, NULL),
('34" Ultrawide Monitor',    '34-ultrawide-monitor',     'accessory', 'displays',    24999, NULL, NULL),
('Gaming Headset',           'gaming-headset',           'accessory', 'audio',       3999,  NULL, NULL),
('RGB Case Fan 3-Pack',      'rgb-case-fan-3-pack',      'accessory', 'components',  1899,  NULL, NULL);
