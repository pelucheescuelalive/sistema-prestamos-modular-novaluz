<?php
/**
 * SCRIPT DE INICIALIZACIÓN DE BASE DE DATOS
 * Sistema de Préstamos Modular - Nova Luz Pro
 */

try {
    // Crear conexión a la base de datos
    $pdo = new PDO('sqlite:prestamos.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Creando estructura de base de datos...\n";
    
    // Crear tabla de clientes con todos los campos necesarios
    $sql_clientes = "
    CREATE TABLE IF NOT EXISTS clientes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        tipo VARCHAR(20) DEFAULT 'Persona',
        cedula VARCHAR(20) UNIQUE NOT NULL,
        nombres VARCHAR(100) NOT NULL,
        apellidos VARCHAR(100) NOT NULL,
        nombre_completo VARCHAR(200) NOT NULL,
        genero VARCHAR(20),
        celular VARCHAR(20) NOT NULL,
        telefono VARCHAR(20),
        nacionalidad VARCHAR(50) DEFAULT 'Dominicano',
        fecha_nacimiento DATE,
        direccion TEXT,
        email VARCHAR(100),
        profesion VARCHAR(100),
        activo INTEGER DEFAULT 1,
        fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql_clientes);
    echo "✓ Tabla clientes creada exitosamente\n";
    
    // Crear tabla de préstamos
    $sql_prestamos = "
    CREATE TABLE IF NOT EXISTS prestamos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cliente_id INTEGER NOT NULL,
        monto_solicitado DECIMAL(10,2) NOT NULL,
        monto_aprobado DECIMAL(10,2) NOT NULL,
        tasa_interes DECIMAL(5,2) NOT NULL,
        plazo_meses INTEGER NOT NULL,
        cuota_mensual DECIMAL(10,2) NOT NULL,
        saldo_pendiente DECIMAL(10,2) NOT NULL,
        estado VARCHAR(20) DEFAULT 'activo',
        fecha_solicitud DATE NOT NULL,
        fecha_aprobacion DATE,
        fecha_primer_pago DATE,
        observaciones TEXT,
        activo INTEGER DEFAULT 1,
        fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id)
    )";
    
    $pdo->exec($sql_prestamos);
    echo "✓ Tabla prestamos creada exitosamente\n";
    
    // Crear tabla de pagos
    $sql_pagos = "
    CREATE TABLE IF NOT EXISTS pagos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        prestamo_id INTEGER NOT NULL,
        numero_cuota INTEGER NOT NULL,
        monto_cuota DECIMAL(10,2) NOT NULL,
        monto_pagado DECIMAL(10,2) NOT NULL,
        fecha_vencimiento DATE NOT NULL,
        fecha_pago DATE,
        estado VARCHAR(20) DEFAULT 'pendiente',
        metodo_pago VARCHAR(50),
        referencia VARCHAR(100),
        observaciones TEXT,
        activo INTEGER DEFAULT 1,
        fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (prestamo_id) REFERENCES prestamos(id)
    )";
    
    $pdo->exec($sql_pagos);
    echo "✓ Tabla pagos creada exitosamente\n";
    
    // Crear tabla de mora
    $sql_mora = "
    CREATE TABLE IF NOT EXISTS mora (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        pago_id INTEGER NOT NULL,
        prestamo_id INTEGER NOT NULL,
        dias_mora INTEGER NOT NULL,
        monto_mora DECIMAL(10,2) NOT NULL,
        tasa_mora DECIMAL(5,2) DEFAULT 5.00,
        estado VARCHAR(20) DEFAULT 'activa',
        fecha_inicio DATE NOT NULL,
        fecha_fin DATE,
        observaciones TEXT,
        activo INTEGER DEFAULT 1,
        fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pago_id) REFERENCES pagos(id),
        FOREIGN KEY (prestamo_id) REFERENCES prestamos(id)
    )";
    
    $pdo->exec($sql_mora);
    echo "✓ Tabla mora creada exitosamente\n";
    
    // Crear tabla de configuración
    $sql_config = "
    CREATE TABLE IF NOT EXISTS configuracion (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        clave VARCHAR(100) UNIQUE NOT NULL,
        valor TEXT NOT NULL,
        descripcion TEXT,
        tipo VARCHAR(20) DEFAULT 'string',
        activo INTEGER DEFAULT 1,
        fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql_config);
    echo "✓ Tabla configuracion creada exitosamente\n";
    
    // Insertar configuraciones básicas
    $configuraciones = [
        ['tasa_interes_default', '15.00', 'Tasa de interés por defecto (%)', 'decimal'],
        ['tasa_mora_default', '5.00', 'Tasa de mora por defecto (%)', 'decimal'],
        ['moneda', 'RD$', 'Moneda del sistema', 'string'],
        ['empresa_nombre', 'Nova Luz Pro', 'Nombre de la empresa', 'string'],
        ['empresa_telefono', '809-000-0000', 'Teléfono de la empresa', 'string']
    ];
    
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO configuracion (clave, valor, descripcion, tipo) VALUES (?, ?, ?, ?)");
    foreach ($configuraciones as $config) {
        $stmt->execute($config);
    }
    echo "✓ Configuraciones básicas insertadas\n";
    
    echo "\n🎉 Base de datos inicializada exitosamente!\n";
    echo "📊 Todas las tablas han sido creadas con los campos necesarios.\n";
    
} catch (PDOException $e) {
    echo "❌ Error creando la base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
}
?>
