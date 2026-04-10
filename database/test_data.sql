USE `apirestaurante`;

-- Limpiamos las tablas si es necesario antes de insertar data
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `reservations`;
TRUNCATE TABLE `time_slots`;
TRUNCATE TABLE `tables`;
TRUNCATE TABLE `zones`;
TRUNCATE TABLE `dishes`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------------
-- Datos de prueba para `users` (Clientes y Administradores)
-- -------------------------------------------------------------
-- Las contraseñas en Laravel por defecto van cifradas con Bcrypt.
-- Aquí empleamos el hash de la palabra "password" para todos o de "123" en caso de ser necesario ajustarlo en el seed.
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Usuario Prueba', 'prueba@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0001', 'client', NOW(), NOW()),
(2, 'Usuario Demo', 'demo@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0002', 'client', NOW(), NOW()),
(3, 'Administrador', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-0000', 'admin', NOW(), NOW()),
(4, 'Carlos García', 'carlos@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-1234', 'client', NOW(), NOW()),
(5, 'María López', 'maria@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-5678', 'client', NOW(), NOW()),
(6, 'Juan Martínez', 'juan@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-9012', 'client', NOW(), NOW()),
(7, 'Rosa Rodríguez', 'rosa@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-3456', 'client', NOW(), NOW()),
(8, 'David Pérez', 'david@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-7890', 'client', NOW(), NOW()),
(9, 'Ana González', 'ana@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-2468', 'client', NOW(), NOW()),
(10, 'Luis Herrera', 'luis@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-1111', 'client', NOW(), NOW()),
(11, 'Paula Ruiz', 'paula@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '555-2222', 'client', NOW(), NOW());

-- -------------------------------------------------------------
-- Datos de prueba para `zones`
-- -------------------------------------------------------------
INSERT INTO `zones` (`id`, `name`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Salón Principal', '🏛️', NOW(), NOW()),
(2, 'Terraza', '🌅', NOW(), NOW()),
(3, 'Área VIP', '✨', NOW(), NOW()),
(4, 'Junto a Ventana', '🪟', NOW(), NOW());

-- -------------------------------------------------------------
-- Datos de prueba para `tables`
-- -------------------------------------------------------------
INSERT INTO `tables` (`id`, `zone_id`, `number`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES
-- Principal
(1, 1, 1, 2, 1, NOW(), NOW()),
(2, 1, 2, 2, 1, NOW(), NOW()),
(3, 1, 3, 4, 1, NOW(), NOW()),
(4, 1, 4, 4, 1, NOW(), NOW()),
(5, 1, 5, 6, 1, NOW(), NOW()),
-- Terraza
(6, 2, 6, 2, 1, NOW(), NOW()),
(7, 2, 7, 4, 1, NOW(), NOW()),
(8, 2, 8, 4, 1, NOW(), NOW()),
-- VIP
(9, 3, 9, 8, 1, NOW(), NOW()),
(10, 3, 10, 8, 1, NOW(), NOW()),
-- Ventana
(11, 4, 11, 2, 1, NOW(), NOW()),
(12, 4, 12, 4, 1, NOW(), NOW());

-- -------------------------------------------------------------
-- Datos de prueba para `dishes`
-- -------------------------------------------------------------
INSERT INTO `dishes` (`id`, `name`, `description`, `price`, `emoji`, `category`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Tagliatelle al Ragù', 'Pasta fresca con ragù de ternera y parmesano', 18.00, '🍝', 'Pastas', 1, NOW(), NOW()),
(2, 'Pizza Margherita', 'Masa madre, tomate San Marzano, mozzarella di bufala', 16.00, '🍕', 'Pizzas', 1, NOW(), NOW()),
(3, 'Bistecca Fiorentina', 'Chuletón de ternera a la parrilla con romero', 34.00, '🥩', 'Carnes', 1, NOW(), NOW()),
(4, 'Tiramisú della Casa', 'Receta original con mascarpone y café espresso', 9.00, '🍮', 'Postres', 1, NOW(), NOW());

-- -------------------------------------------------------------
-- Datos de prueba para `time_slots`
-- -------------------------------------------------------------
INSERT INTO `time_slots` (`id`, `start_time`, `duration`, `created_at`, `updated_at`) VALUES
(1, '12:00:00', 60, NOW(), NOW()),
(2, '12:30:00', 60, NOW(), NOW()),
(3, '13:00:00', 60, NOW(), NOW()),
(4, '13:30:00', 60, NOW(), NOW()),
(5, '14:00:00', 60, NOW(), NOW()),
(6, '14:30:00', 60, NOW(), NOW()),
(7, '15:00:00', 60, NOW(), NOW()),
(8, '19:00:00', 60, NOW(), NOW()),
(9, '19:30:00', 60, NOW(), NOW()),
(10, '20:00:00', 60, NOW(), NOW()),
(11, '20:30:00', 60, NOW(), NOW()),
(12, '21:00:00', 60, NOW(), NOW()),
(13, '21:30:00', 60, NOW(), NOW()),
(14, '22:00:00', 60, NOW(), NOW());

-- -------------------------------------------------------------
-- Datos de prueba para `reservations`
-- -------------------------------------------------------------
INSERT INTO `reservations` (`id`, `user_id`, `table_id`, `date`, `start_time`, `end_time`, `persons`, `status`, `tipopago`, `notes`, `created_at`, `updated_at`) VALUES
(1, 4, 5, '2026-03-01', '12:30:00', '14:00:00', 2, 'reservado', 'Efectivo', 'Reserva importada automáticamente', NOW(), NOW()),
(2, 5, 7, '2026-03-01', '13:00:00', '14:30:00', 4, 'reservado', 'Efectivo', 'Reserva importada automáticamente', NOW(), NOW()),
(3, 6, 2, '2026-02-28', '13:30:00', '15:00:00', 3, 'reservado', 'Efectivo', 'Reserva importada automáticamente', NOW(), NOW()),
(4, 7, 3, '2026-02-28', '19:00:00', '21:00:00', 5, 'reservado', 'Efectivo', 'Reserva importada automáticamente', NOW(), NOW()),
(5, 8, 4, '2026-02-28', '20:00:00', '22:00:00', 4, 'reservado', 'Efectivo', 'Reserva importada automáticamente', NOW(), NOW()),
(6, 9, 6, '2026-02-28', '19:30:00', '21:00:00', 2, 'reservado', 'Efectivo', 'Reserva importada automáticamente', NOW(), NOW()),
(7, 10, 9, '2026-03-02', '20:00:00', '22:30:00', 6, 'reservado', 'Efectivo', 'Reserva importada automáticamente', NOW(), NOW()),
(8, 11, 1, '2026-03-01', '14:00:00', '16:00:00', 2, 'reservado', 'Efectivo', 'Reserva importada automáticamente', NOW(), NOW());
