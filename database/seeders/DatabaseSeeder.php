<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use App\Models\Producto;
use App\Models\Agenda;
use App\Models\Ticket;
use App\Models\DetalleTicket;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // 1. Empleados
        $empleados = [
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'email' => 'carlos@barberia.com',
                'telefono' => '999-123-4567',
                'puesto' => 'Barbero Senior',
                'hora_entrada' => '09:00:00',
                'hora_salida' => '18:00:00',
                'dias_laborales' => json_encode(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado']),
                'salario' => 8000.00,
                'comision_porcentaje' => 15.00,
                'fecha_ingreso' => '2023-01-15',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'María',
                'apellido' => 'González',
                'email' => 'maria@barberia.com',
                'telefono' => '999-234-5678',
                'puesto' => 'Estilista',
                'hora_entrada' => '10:00:00',
                'hora_salida' => '19:00:00',
                'dias_laborales' => json_encode(['martes', 'miercoles', 'jueves', 'viernes', 'sabado']),
                'salario' => 7500.00,
                'comision_porcentaje' => 12.00,
                'fecha_ingreso' => '2023-03-20',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Martínez',
                'email' => 'luis@barberia.com',
                'telefono' => '999-345-6789',
                'puesto' => 'Barbero Junior',
                'hora_entrada' => '09:00:00',
                'hora_salida' => '17:00:00',
                'dias_laborales' => json_encode(['lunes', 'miercoles', 'viernes', 'sabado', 'domingo']),
                'salario' => 6000.00,
                'comision_porcentaje' => 10.00,
                'fecha_ingreso' => '2024-06-01',
                'estado' => 'activo',
            ],
        ];

        foreach ($empleados as $emp) {
            Empleado::create($emp);
        }

        // 2. Clientes
        $clientes = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'email' => 'juan.perez@email.com',
                'telefono' => '999-111-2222',
                'fecha_nacimiento' => '1990-05-15',
                'genero' => 'masculino',
                'acepta_marketing' => true,
                'puntos_fidelidad' => 150,
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'López',
                'email' => 'ana.lopez@email.com',
                'telefono' => '999-222-3333',
                'fecha_nacimiento' => '1988-08-22',
                'genero' => 'femenino',
                'acepta_marketing' => true,
                'puntos_fidelidad' => 320,
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Sánchez',
                'email' => null,
                'telefono' => '999-333-4444',
                'fecha_nacimiento' => '1995-12-10',
                'genero' => 'masculino',
                'acepta_marketing' => false,
                'puntos_fidelidad' => 50,
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Fernández',
                'email' => 'laura.f@email.com',
                'telefono' => '999-444-5555',
                'fecha_nacimiento' => '1992-03-18',
                'genero' => 'femenino',
                'acepta_marketing' => true,
                'puntos_fidelidad' => 200,
            ],
        ];

        foreach ($clientes as $cli) {
            Cliente::create($cli);
        }

        // 3. Servicios
        $servicios = [
            [
                'nombre' => 'Corte Caballero',
                'descripcion' => 'Corte de cabello clásico para hombre',
                'precio' => 150.00,
                'duracion_minutos' => 30,
                'categoria' => 'Cortes',
                'activo' => true,
            ],
            [
                'nombre' => 'Corte + Barba',
                'descripcion' => 'Corte de cabello y arreglo de barba',
                'precio' => 200.00,
                'duracion_minutos' => 45,
                'categoria' => 'Cortes',
                'activo' => true,
            ],
            [
                'nombre' => 'Afeitado Clásico',
                'descripcion' => 'Afeitado tradicional con navaja y toallas calientes',
                'precio' => 180.00,
                'duracion_minutos' => 40,
                'categoria' => 'Afeitado',
                'activo' => true,
            ],
            [
                'nombre' => 'Tinte Completo',
                'descripcion' => 'Aplicación de tinte de color',
                'precio' => 350.00,
                'duracion_minutos' => 90,
                'categoria' => 'Coloración',
                'activo' => true,
            ],
            [
                'nombre' => 'Corte Niño',
                'descripcion' => 'Corte de cabello para niños',
                'precio' => 100.00,
                'duracion_minutos' => 20,
                'categoria' => 'Cortes',
                'activo' => true,
            ],
            [
                'nombre' => 'Peinado de Dama',
                'descripcion' => 'Peinado y brushing',
                'precio' => 250.00,
                'duracion_minutos' => 60,
                'categoria' => 'Peinados',
                'activo' => true,
            ],
        ];

        foreach ($servicios as $serv) {
            Servicio::create($serv);
        }

        // 4. Productos
        $productos = [
            [
                'nombre' => 'Pomada para Cabello',
                'codigo' => 'POM-001',
                'marca' => 'Suavecito',
                'descripcion' => 'Pomada de fijación fuerte',
                'precio_venta' => 250.00,
                'precio_compra' => 150.00,
                'stock' => 25,
                'stock_minimo' => 10,
                'categoria' => 'Styling',
                'activo' => true,
            ],
            [
                'nombre' => 'Shampoo Anticaspa',
                'codigo' => 'SHA-001',
                'marca' => 'Head & Shoulders',
                'descripcion' => 'Shampoo medicado 400ml',
                'precio_venta' => 180.00,
                'precio_compra' => 120.00,
                'stock' => 40,
                'stock_minimo' => 15,
                'categoria' => 'Cuidado',
                'activo' => true,
            ],
            [
                'nombre' => 'Cera para Bigote',
                'codigo' => 'CER-001',
                'marca' => 'Beard Bros',
                'descripcion' => 'Cera modeladora para bigote',
                'precio_venta' => 150.00,
                'precio_compra' => 90.00,
                'stock' => 15,
                'stock_minimo' => 5,
                'categoria' => 'Barba',
                'activo' => true,
            ],
            [
                'nombre' => 'Aceite para Barba',
                'codigo' => 'ACE-001',
                'marca' => 'Viking Revolution',
                'descripcion' => 'Aceite nutritivo 50ml',
                'precio_venta' => 280.00,
                'precio_compra' => 180.00,
                'stock' => 30,
                'stock_minimo' => 10,
                'categoria' => 'Barba',
                'activo' => true,
            ],
            [
                'nombre' => 'Gel Fijador Extremo',
                'codigo' => 'GEL-001',
                'marca' => 'Gatsby',
                'descripcion' => 'Gel de fijación extrema',
                'precio_venta' => 120.00,
                'precio_compra' => 70.00,
                'stock' => 50,
                'stock_minimo' => 20,
                'categoria' => 'Styling',
                'activo' => true,
            ],
        ];

        foreach ($productos as $prod) {
            Producto::create($prod);
        }

        // 5. Agendas (Citas)
        $agendas = [
            [
                'cliente_id' => 1,
                'empleado_id' => 1,
                'fecha_hora_inicio' => now()->addDays(1)->setTime(10, 0),
                'fecha_hora_fin' => now()->addDays(1)->setTime(10, 30),
                'tipo' => 'estandar',
                'estado' => 'confirmada',
            ],
            [
                'cliente_id' => 2,
                'empleado_id' => 2,
                'fecha_hora_inicio' => now()->addDays(1)->setTime(11, 0),
                'fecha_hora_fin' => now()->addDays(1)->setTime(12, 0),
                'tipo' => 'estandar',
                'estado' => 'pendiente',
            ],
            [
                'cliente_id' => 3,
                'empleado_id' => 1,
                'fecha_hora_inicio' => now()->addDays(2)->setTime(15, 0),
                'fecha_hora_fin' => now()->addDays(2)->setTime(15, 45),
                'tipo' => 'express',
                'estado' => 'confirmada',
            ],
            // Cita completada (histórica)
            [
                'cliente_id' => 4,
                'empleado_id' => 3,
                'fecha_hora_inicio' => now()->subDays(2)->setTime(14, 0),
                'fecha_hora_fin' => now()->subDays(2)->setTime(14, 30),
                'tipo' => 'estandar',
                'estado' => 'completada',
            ],
        ];

        foreach ($agendas as $ag) {
            $agenda = Agenda::create($ag);

            // Agregar servicios a cada agenda
            if ($agenda->id == 1) {
                $agenda->servicios()->attach(1, [
                    'precio' => 150.00,
                    'duracion_minutos' => 30,
                ]);
            } elseif ($agenda->id == 2) {
                $agenda->servicios()->attach([
                    4 => ['precio' => 350.00, 'duracion_minutos' => 60],
                ]);
            } elseif ($agenda->id == 3) {
                $agenda->servicios()->attach(2, [
                    'precio' => 200.00,
                    'duracion_minutos' => 45,
                ]);
            } elseif ($agenda->id == 4) {
                $agenda->servicios()->attach(5, [
                    'precio' => 100.00,
                    'duracion_minutos' => 20,
                ]);
            }
        }

        // 6. Tickets (Ventas completadas)
        $ticket1 = Ticket::create([
            'numero_ticket' => 'VT-2025-00001',
            'cliente_id' => 1,
            'empleado_id' => 1,
            'subtotal' => 150.00,
            'descuento' => 0,
            'impuesto' => 24.00, // 16% IVA
            'total' => 174.00,
            'metodo_pago' => 'efectivo',
            'estado_pago' => 'pagado',
            'fecha' => now()->subDays(3),
        ]);

        // Detalle: Servicio
        DetalleTicket::create([
            'ticket_id' => $ticket1->id,
            'vendible_type' => 'App\Models\Servicio',
            'vendible_id' => 1,
            'cantidad' => 1,
            'precio_unitario' => 150.00,
            'subtotal' => 150.00,
            'empleado_id' => 1,
        ]);

        // Ticket 2: Servicio + Producto
        $ticket2 = Ticket::create([
            'numero_ticket' => 'VT-2025-00002',
            'cliente_id' => 2,
            'empleado_id' => 2,
            'subtotal' => 550.00,
            'descuento' => 50.00,
            'impuesto' => 80.00,
            'total' => 580.00,
            'metodo_pago' => 'tarjeta',
            'estado_pago' => 'pagado',
            'fecha' => now()->subDays(1),
        ]);

        DetalleTicket::create([
            'ticket_id' => $ticket2->id,
            'vendible_type' => 'App\Models\Servicio',
            'vendible_id' => 4, // Tinte
            'cantidad' => 1,
            'precio_unitario' => 350.00,
            'subtotal' => 350.00,
            'empleado_id' => 2,
        ]);

        DetalleTicket::create([
            'ticket_id' => $ticket2->id,
            'vendible_type' => 'App\Models\Producto',
            'vendible_id' => 2, // Shampoo
            'cantidad' => 1,
            'precio_unitario' => 180.00,
            'subtotal' => 180.00,
        ]);

        // Ticket 3: Solo productos
        $ticket3 = Ticket::create([
            'numero_ticket' => 'VT-2025-00003',
            'cliente_id' => null, // Cliente sin registro
            'empleado_id' => 1,
            'subtotal' => 530.00,
            'descuento' => 0,
            'impuesto' => 84.80,
            'total' => 614.80,
            'metodo_pago' => 'efectivo',
            'estado_pago' => 'pagado',
            'fecha' => now(),
        ]);

        DetalleTicket::create([
            'ticket_id' => $ticket3->id,
            'vendible_type' => 'App\Models\Producto',
            'vendible_id' => 1, // Pomada
            'cantidad' => 2,
            'precio_unitario' => 250.00,
            'subtotal' => 500.00,
        ]);

        DetalleTicket::create([
            'ticket_id' => $ticket3->id,
            'vendible_type' => 'App\Models\Producto',
            'vendible_id' => 4, // Aceite
            'cantidad' => 1,
            'precio_unitario' => 280.00,
            'subtotal' => 280.00,
        ]);


    }
}
