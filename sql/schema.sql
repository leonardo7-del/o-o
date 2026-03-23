-- Ejecutar este script una vez en Azure SQL para crear las tablas

CREATE TABLE categorias (
    id          INT IDENTITY(1,1) PRIMARY KEY,
    nombre      NVARCHAR(100)  NOT NULL,
    descripcion NVARCHAR(255),
    creado_en   DATETIME2 DEFAULT GETDATE()
);

CREATE TABLE productos (
    id             INT IDENTITY(1,1) PRIMARY KEY,
    codigo         NVARCHAR(50)   NOT NULL UNIQUE,
    nombre         NVARCHAR(150)  NOT NULL,
    descripcion    NVARCHAR(500),
    categoria_id   INT            REFERENCES categorias(id),
    precio         DECIMAL(10,2)  NOT NULL DEFAULT 0,
    stock          INT            NOT NULL DEFAULT 0,
    stock_minimo   INT            NOT NULL DEFAULT 5,
    unidad         NVARCHAR(30)   DEFAULT 'unidad',
    creado_en      DATETIME2      DEFAULT GETDATE(),
    actualizado_en DATETIME2      DEFAULT GETDATE()
);

CREATE TABLE movimientos (
    id          INT IDENTITY(1,1) PRIMARY KEY,
    producto_id INT            NOT NULL REFERENCES productos(id),
    tipo        NVARCHAR(10)   NOT NULL CHECK (tipo IN ('entrada','salida')),
    cantidad    INT            NOT NULL,
    motivo      NVARCHAR(255),
    usuario     NVARCHAR(100)  DEFAULT 'admin',
    creado_en   DATETIME2      DEFAULT GETDATE()
);

INSERT INTO categorias (nombre, descripcion) VALUES
    ('Electrónica',   'Dispositivos y componentes electrónicos'),
    ('Papelería',     'Útiles de escritorio y oficina'),
    ('Herramientas',  'Herramientas manuales y eléctricas'),
    ('Consumibles',   'Materiales de consumo diario');

INSERT INTO productos (codigo, nombre, descripcion, categoria_id, precio, stock, stock_minimo, unidad) VALUES
    ('ELEC-001', 'Laptop HP 15"',       'Laptop Intel Core i5, 8GB RAM',  1, 2500.00, 10, 3,  'unidad'),
    ('ELEC-002', 'Mouse inalámbrico',   'Mouse USB 2.4GHz',               1,   45.00, 25, 5,  'unidad'),
    ('PAP-001',  'Resma papel A4',      'Papel bond 75g, 500 hojas',      2,   18.50, 50, 10, 'paquete'),
    ('PAP-002',  'Lapiceros azules',    'Caja x 50 unidades',             2,   12.00,  8, 5,  'caja'),
    ('HERR-001', 'Destornillador set',  'Juego 12 piezas Phillips/Plano', 3,   35.00, 15, 4,  'set'),
    ('CONS-001', 'Tóner HP LaserJet',   'Tóner negro 2000 páginas',       4,  180.00,  4, 2,  'unidad');
