-- ============================================================================
-- TEST DATA FOR PEDIDOS (ORDERS) MODULE
-- ============================================================================
-- Este archivo contiene datos de prueba para el módulo de Pedidos del CRM
-- Ejecutar después de tener la base de datos principal creada
-- ============================================================================

USE kickverse;

-- Asegurarse de que existen clientes de prueba
-- (Si ya existen, estos INSERTs fallarán pero no afectará al resto)

INSERT IGNORE INTO customers (customer_id, email, full_name, phone, telegram_username, whatsapp_number, preferred_language, customer_status, loyalty_tier, loyalty_points, total_orders_count, total_spent, registration_date)
VALUES
(1, 'juan.perez@example.com', 'Juan Pérez García', '+34612345678', 'juanperez', '+34612345678', 'es', 'active', 'gold', 350, 5, 450.00, '2024-01-15 10:30:00'),
(2, 'maria.lopez@example.com', 'María López Sánchez', '+34623456789', 'marialopez', '+34623456789', 'es', 'active', 'silver', 180, 3, 275.00, '2024-02-20 14:15:00'),
(3, NULL, 'Carlos Rodríguez', NULL, 'carlosrod', '+34634567890', 'es', 'active', 'standard', 50, 1, 24.99, '2024-03-10 16:45:00'),
(4, 'ana.martinez@example.com', 'Ana Martínez Ruiz', '+34645678901', NULL, NULL, 'es', 'active', 'platinum', 850, 12, 1200.00, '2023-11-05 09:20:00'),
(5, NULL, 'Pedro Gómez', NULL, 'pedrogomez', '+34656789012', 'es', 'active', 'gold', 420, 6, 580.00, '2024-01-28 11:10:00');

-- Asegurarse de que existen direcciones de envío
INSERT IGNORE INTO shipping_addresses (address_id, customer_id, is_default, recipient_name, phone, email, street_address, city, province, postal_code, country)
VALUES
(1, 1, 1, 'Juan Pérez García', '+34612345678', 'juan.perez@example.com', 'Calle Gran Vía, 28', 'Madrid', 'Madrid', '28013', 'España'),
(2, 2, 1, 'María López Sánchez', '+34623456789', 'maria.lopez@example.com', 'Avenida Diagonal, 450', 'Barcelona', 'Barcelona', '08006', 'España'),
(3, 3, 1, 'Carlos Rodríguez', '+34634567890', NULL, 'Calle Alameda, 15', 'Valencia', 'Valencia', '46001', 'España'),
(4, 4, 1, 'Ana Martínez Ruiz', '+34645678901', 'ana.martinez@example.com', 'Paseo de la Castellana, 120', 'Madrid', 'Madrid', '28046', 'España'),
(5, 5, 1, 'Pedro Gómez', '+34656789012', NULL, 'Calle Sevilla, 8', 'Sevilla', 'Sevilla', '41001', 'España');

-- Insertar pedidos de prueba con diferentes estados
INSERT INTO orders (order_id, customer_id, order_type, order_source, order_status, payment_status, subtotal, discount_amount, shipping_cost, total_amount, payment_method, shipping_address_id, order_date)
VALUES
-- Pedido 1: Completado y entregado
(1, 1, 'catalog', 'web', 'delivered', 'completed', 74.97, 7.50, 0.00, 67.47, 'oxapay', 1, '2024-10-15 10:30:00'),

-- Pedido 2: Enviado con tracking
(2, 2, 'catalog', 'telegram', 'shipped', 'completed', 49.98, 0.00, 0.00, 49.98, 'telegram', 2, '2024-10-28 14:20:00'),

-- Pedido 3: En proceso, pago completado
(3, 3, 'catalog', 'web', 'processing', 'completed', 24.99, 0.00, 5.99, 30.98, 'oxapay', 3, '2024-11-01 16:45:00'),

-- Pedido 4: Pendiente de pago
(4, 4, 'mystery_box', 'web', 'pending_payment', 'pending', 119.99, 0.00, 0.00, 119.99, NULL, 4, '2024-11-04 09:15:00'),

-- Pedido 5: Cancelado
(5, 5, 'catalog', 'whatsapp', 'cancelled', 'refunded', 74.97, 0.00, 0.00, 74.97, 'whatsapp', 5, '2024-10-20 11:30:00'),

-- Pedido 6: Enviado con tracking reciente
(6, 1, 'catalog', 'web', 'shipped', 'completed', 99.96, 10.00, 0.00, 89.96, 'oxapay', 1, '2024-11-03 15:20:00'),

-- Pedido 7: Suscripción inicial
(7, 2, 'subscription_initial', 'web', 'delivered', 'completed', 24.99, 0.00, 0.00, 24.99, 'oxapay', 2, '2024-10-01 10:00:00'),

-- Pedido 8: Drop - En proceso
(8, 3, 'drop', 'web', 'processing', 'completed', 24.99, 0.00, 5.99, 30.98, 'telegram', 3, '2024-11-05 12:30:00'),

-- Pedido 9: Entregado hace tiempo
(9, 4, 'catalog', 'web', 'delivered', 'completed', 149.94, 15.00, 0.00, 134.94, 'oxapay', 4, '2024-09-15 08:20:00'),

-- Pedido 10: Pending payment hace 2 días
(10, 5, 'catalog', 'instagram', 'pending_payment', 'pending', 49.98, 0.00, 0.00, 49.98, NULL, 5, '2024-11-03 18:45:00');

-- Actualizar fechas de envío y entrega para pedidos completados
UPDATE orders SET shipped_date = '2024-10-16', delivered_date = '2024-10-18' WHERE order_id = 1;
UPDATE orders SET shipped_date = '2024-10-29', tracking_number = 'SEUR123456789ES', carrier = 'SEUR' WHERE order_id = 2;
UPDATE orders SET shipped_date = '2024-11-04', tracking_number = 'MRW987654321ES', carrier = 'MRW' WHERE order_id = 6;
UPDATE orders SET shipped_date = '2024-10-02', delivered_date = '2024-10-04' WHERE order_id = 7;
UPDATE orders SET shipped_date = '2024-09-16', delivered_date = '2024-09-19' WHERE order_id = 9;

-- Insertar items de pedido (necesitamos productos existentes)
-- Asumiendo que existen productos con IDs 1-10 y variantes

-- Pedido 1 items (3 camisetas)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(1, 1, 1, 1, 24.99, 1, 1.99, 1, 'PÉREZ', '10', 2.99, 29.97),
(1, 2, 5, 1, 24.99, 0, 0, 1, 'JUAN', '7', 2.99, 27.98),
(1, 3, 9, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99);

-- Pedido 2 items (2 camisetas)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(2, 4, 13, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99),
(2, 5, 17, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99);

-- Pedido 3 items (1 camiseta)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(3, 1, 2, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99);

-- Pedido 4 items (Mystery Box)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(4, 15, 50, 1, 119.99, 0, 0, 0, NULL, NULL, 0, 119.99);

-- Pedido 5 items (3 camisetas - cancelado)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(5, 2, 6, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99),
(5, 3, 10, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99),
(5, 6, 21, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99);

-- Pedido 6 items (4 camisetas con personalización)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(6, 1, 3, 1, 24.99, 1, 1.99, 1, 'GARCÍA', '9', 2.99, 29.97),
(6, 2, 7, 1, 24.99, 1, 1.99, 1, 'PÉREZ', '10', 2.99, 29.97),
(6, 4, 14, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99),
(6, 5, 18, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99);

-- Pedido 7 items (Suscripción)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(7, 1, 1, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99);

-- Pedido 8 items (Drop)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(8, 7, 25, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99);

-- Pedido 9 items (6 camisetas - pedido grande)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(9, 1, 1, 1, 24.99, 1, 1.99, 1, 'MARTÍNEZ', '5', 2.99, 29.97),
(9, 2, 5, 1, 24.99, 1, 1.99, 0, NULL, NULL, 0, 26.98),
(9, 3, 9, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99),
(9, 4, 13, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99),
(9, 5, 17, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99),
(9, 6, 21, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99);

-- Pedido 10 items (2 camisetas - pendiente pago)
INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, has_patches, patches_price, has_personalization, personalization_name, personalization_number, personalization_price, subtotal)
VALUES
(10, 8, 29, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99),
(10, 9, 33, 1, 24.99, 0, 0, 0, NULL, NULL, 0, 24.99);

-- Añadir notas del administrador a algunos pedidos
UPDATE orders SET admin_notes = 'Cliente VIP - prioridad en envío' WHERE order_id = 1;
UPDATE orders SET admin_notes = 'Pedido desde Telegram - cliente pidió envío urgente' WHERE order_id = 2;
UPDATE orders SET admin_notes = 'Cancelado por solicitud del cliente - stock insuficiente de talla solicitada' WHERE order_id = 5;
UPDATE orders SET customer_notes = 'Por favor, enviar en caja discreta sin logos' WHERE order_id = 6;

-- ============================================================================
-- VERIFICACIÓN
-- ============================================================================

-- Consulta para verificar los pedidos creados
SELECT
    o.order_id,
    o.order_status,
    o.payment_status,
    c.full_name as customer_name,
    o.total_amount,
    o.order_date,
    COUNT(oi.order_item_id) as items_count
FROM orders o
JOIN customers c ON o.customer_id = c.customer_id
LEFT JOIN order_items oi ON o.order_id = oi.order_id
GROUP BY o.order_id
ORDER BY o.order_date DESC;

-- Estadísticas de pedidos por estado
SELECT
    order_status,
    COUNT(*) as count,
    SUM(total_amount) as total_revenue
FROM orders
GROUP BY order_status
ORDER BY count DESC;

-- Pedidos con tracking
SELECT
    order_id,
    tracking_number,
    carrier,
    order_status
FROM orders
WHERE tracking_number IS NOT NULL;

-- ============================================================================
-- NOTAS
-- ============================================================================
--
-- Este script crea 10 pedidos de prueba con diferentes estados:
-- - 3 Entregados (delivered)
-- - 2 Enviados con tracking (shipped)
-- - 2 En proceso (processing)
-- - 2 Pendiente de pago (pending_payment)
-- - 1 Cancelado (cancelled)
--
-- Los pedidos incluyen diferentes configuraciones:
-- - Con y sin personalización
-- - Con y sin parches
-- - Diferentes métodos de pago
-- - Diferentes orígenes (web, telegram, whatsapp, instagram)
-- - Diferentes tipos (catalog, mystery_box, subscription, drop)
-- - Con y sin descuentos
-- - Con y sin gastos de envío
--
-- Para probar el módulo de pedidos, puedes:
-- 1. Ver la lista completa en /admin/pedidos
-- 2. Filtrar por diferentes estados
-- 3. Buscar por ID, cliente o tracking
-- 4. Abrir los modales de detalle
-- 5. Actualizar estados
-- 6. Añadir tracking a pedidos en proceso
-- 7. Cancelar pedidos pendientes
--
-- ============================================================================
