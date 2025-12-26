<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mascota;
use App\Models\Producto;
use App\Models\CasoRescate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuarios - Solo crear si no existen
        if (User::where('email', 'usuario@email.com')->count() == 0) {
            User::create([
                'nombre_usuario' => 'usuario123',
                'nombre_completo' => 'Juan Pérez',
                'email' => 'usuario@email.com',
                'telefono' => '987654321',
                'direccion' => 'Av. Los Olivos 123',
                'password' => '12345',
                'rol' => 'usuario',
            ]);
        }

        if (User::where('email', 'admin@email.com')->count() == 0) {
            User::create([
                'nombre_usuario' => 'admin',
                'nombre_completo' => 'Administrador',
                'email' => 'admin@email.com',
                'telefono' => '999666333',
                'direccion' => 'Oficina Central',
                'password' => '12345',
                'rol' => 'admin',
            ]);
        }

        // Mascotas - Solo crear si no existen
        if (Mascota::count() == 0) {
            $mascotas = [
            ['nombre' => 'Max', 'tipo' => 'perros', 'raza' => 'Labrador', 'edad' => '3 años', 'descripcion' => 'Perro muy juguetón y amigable, perfecto para familias con niños. Le encanta correr y jugar en el parque.', 'imagen' => 'perro1.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-01-15'],
            ['nombre' => 'Rocky', 'tipo' => 'perros', 'raza' => 'Pastor Alemán', 'edad' => '5 años', 'descripcion' => 'Guardián leal y protector. Entrenado en obediencia básica, ideal para casas con jardín.', 'imagen' => 'perro2.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-02-20'],
            ['nombre' => 'Bella', 'tipo' => 'perros', 'raza' => 'Golden Retriever', 'edad' => '2 años', 'descripcion' => 'Dulce y cariñosa, ama a los niños. Muy tranquila en casa pero le gusta pasear.', 'imagen' => 'perro3.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-03-10'],
            ['nombre' => 'Luna', 'tipo' => 'gatos', 'raza' => 'Persa', 'edad' => '2 años', 'descripcion' => 'Gata tranquila y elegante. Le gusta dormir en lugares cálidos y recibir mimos.', 'imagen' => 'gato1.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-01-25'],
            ['nombre' => 'Michi', 'tipo' => 'gatos', 'raza' => 'Siamés', 'edad' => '1 año', 'descripcion' => 'Muy activo y juguetón. Le encanta trepar y explorar cada rincón de la casa.', 'imagen' => 'gato2.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-02-15'],
            ['nombre' => 'Garfield', 'tipo' => 'gatos', 'raza' => 'Naranja', 'edad' => '4 años', 'descripcion' => 'Gato tranquilo que disfruta de la comida y las siestas. Perfecto para apartamentos.', 'imagen' => 'gato3.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-03-05'],
            ['nombre' => 'Copito', 'tipo' => 'otros', 'raza' => 'Holland Lop', 'edad' => '1 año', 'descripcion' => 'Conejo súper tierno y cariñoso. Le gusta que lo acaricien y es muy sociable.', 'imagen' => 'conejo1.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-02-01'],
            ['nombre' => 'Tambor', 'tipo' => 'otros', 'raza' => 'Belier', 'edad' => '6 meses', 'descripcion' => 'Juguetón y curioso. Le encanta explorar y saltar por toda la casa.', 'imagen' => 'conejo2.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-02-28'],
            ['nombre' => 'Piolín', 'tipo' => 'otros', 'raza' => 'Canario', 'edad' => '1 año', 'descripcion' => 'Canta hermoso por las mañanas. Muy alegre y colorido, perfecto para hogares tranquilos.', 'imagen' => 'ave1.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-03-15'],
            ['nombre' => 'Kiwi', 'tipo' => 'otros', 'raza' => 'Periquito', 'edad' => '2 años', 'descripcion' => 'Sociable y puede aprender a imitar sonidos. Le gusta interactuar con las personas.', 'imagen' => 'ave2.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-03-20'],
            ['nombre' => 'Stuart', 'tipo' => 'otros', 'raza' => 'Sirio', 'edad' => '6 meses', 'descripcion' => 'Pequeño y adorable. Perfecto para niños, requiere poco espacio y cuidados básicos.', 'imagen' => 'hamster1.jpg', 'estado' => 'disponible', 'fecha_ingreso' => '2024-03-25'],
        ];

            foreach ($mascotas as $mascota) {
                Mascota::create($mascota);
            }
        }

        // Productos - Solo crear si no existen
        if (Producto::count() == 0) {
            $productos = [
            ['nombre' => 'Alimento Premium para Perros', 'descripcion' => 'Croquetas nutritivas con carne de pollo real. Bolsa de 15kg. Ideal para perros adultos de todas las razas.', 'precio' => 120.00, 'categoria' => 'alimento', 'imagen' => 'comida-perro.jpg', 'stock' => 50],
            ['nombre' => 'Alimento para Gatos', 'descripcion' => 'Croquetas con salmón y atún. Bolsa de 10kg. Favorece el pelaje brillante y salud digestiva.', 'precio' => 95.00, 'categoria' => 'alimento', 'imagen' => 'comida-gato.jpg', 'stock' => 45],
            ['nombre' => 'Set de Juguetes para Perro', 'descripcion' => 'Pack de 5 juguetes resistentes: pelota, cuerda, hueso masticable y más. Perfecto para mantener a tu perro activo.', 'precio' => 45.00, 'categoria' => 'juguete', 'imagen' => 'juguete-perro.jpg', 'stock' => 30],
            ['nombre' => 'Rascador para Gatos', 'descripcion' => 'Torre rascadora de 80cm con plataformas y juguetes colgantes. Protege tus muebles mientras tu gato juega.', 'precio' => 180.00, 'categoria' => 'juguete', 'imagen' => 'rascador-gato.jpg', 'stock' => 20],
            ['nombre' => 'Collar Ajustable con Correa', 'descripcion' => 'Collar acolchado de nylon resistente con correa de 1.5m. Disponible en varios colores. Talla M.', 'precio' => 35.00, 'categoria' => 'accesorio', 'imagen' => 'collar-perro.jpg', 'stock' => 60],
            ['nombre' => 'Cama Acolchada para Mascotas', 'descripcion' => 'Cama ortopédica de memory foam. Funda lavable. Tamaño grande (90x70cm). Perfecta para el descanso.', 'precio' => 150.00, 'categoria' => 'accesorio', 'imagen' => 'cama-mascota.jpg', 'stock' => 25],
            ['nombre' => 'Shampoo Hipoalergénico', 'descripcion' => 'Shampoo suave sin parabenos. Con aloe vera y avena. 500ml. Ideal para pieles sensibles.', 'precio' => 28.00, 'categoria' => 'higiene', 'imagen' => 'shampoo.jpg', 'stock' => 100],
            ['nombre' => 'Arena Aglomerante para Gatos', 'descripcion' => 'Arena premium con control de olores. 10kg. Aglomerante y fácil de limpiar. Libre de polvo.', 'precio' => 42.00, 'categoria' => 'higiene', 'imagen' => 'arena-gato.jpg', 'stock' => 70],
            ['nombre' => 'Suplemento Vitamínico', 'descripcion' => 'Multivitamínico completo para mascotas. 60 tabletas masticables. Fortalece el sistema inmune.', 'precio' => 65.00, 'categoria' => 'salud', 'imagen' => 'vitaminas.jpg', 'stock' => 40],
            ['nombre' => 'Tratamiento Antipulgas', 'descripcion' => 'Pipeta antipulgas y garrapatas de larga duración. Pack de 3 unidades. Protección por 3 meses.', 'precio' => 85.00, 'categoria' => 'salud', 'imagen' => 'antipulgas.jpg', 'stock' => 35],
        ];

            foreach ($productos as $producto) {
                Producto::create($producto);
            }
        }

        // Casos de rescate - Solo crear si no existen
        if (CasoRescate::count() == 0) {
            $max = Mascota::where('nombre', 'Max')->first();
            $rocky = Mascota::where('nombre', 'Rocky')->first();
            $bella = Mascota::where('nombre', 'Bella')->first();
            $luna = Mascota::where('nombre', 'Luna')->first();
            $michi = Mascota::where('nombre', 'Michi')->first();
            $copito = Mascota::where('nombre', 'Copito')->first();

            if ($max) {
                CasoRescate::create([
                    'id_mascota' => $max->id_mascota,
                    'situacion' => 'Encontrado abandonado en una carretera con desnutrición severa.',
                    'historia' => 'Max fue rescatado hace 2 semanas en condiciones deplorables. Estaba extremadamente delgado y asustado. Ahora está recibiendo tratamiento veterinario y está ganando peso gradualmente. A pesar de su pasado, muestra signos de querer confiar en las personas nuevamente. Necesita un hogar paciente que le ayude a recuperar la confianza.',
                    'tratamiento' => 'En rehabilitación nutricional y terapia de socialización.',
                    'urgencia' => 'alta',
                    'fecha_rescate' => '2024-03-01',
                ]);
            }

            if ($rocky) {
                CasoRescate::create([
                    'id_mascota' => $rocky->id_mascota,
                    'situacion' => 'Rescatado de una situación de maltrato físico.',
                    'historia' => 'Rocky fue víctima de maltrato físico por parte de sus antiguos dueños. Llegó al refugio con múltiples lesiones y un miedo extremo a los humanos. Ha pasado 3 meses en rehabilitación física y emocional. Poco a poco está aprendiendo que no todas las personas son malas. Tiene algunos problemas de movilidad en su pata trasera pero eso no le impide ser cariñoso.',
                    'tratamiento' => 'Fisioterapia semanal y terapia conductual.',
                    'urgencia' => 'media',
                    'fecha_rescate' => '2024-02-01',
                ]);
            }

            if ($bella) {
                CasoRescate::create([
                    'id_mascota' => $bella->id_mascota,
                    'situacion' => 'Abandonada por ser considerada "muy vieja".',
                    'historia' => 'Bella fue dejada en la puerta del refugio con una nota que decía "ya no la queremos, es muy vieja". Tiene solo 2 años y está en perfecto estado de salud. Es una perra noble, tranquila y educada. Simplemente fue descartada por una familia que no valoró su lealtad.',
                    'tratamiento' => 'Chequeos veterinarios de rutina.',
                    'urgencia' => 'baja',
                    'fecha_rescate' => '2024-02-15',
                ]);
            }

            if ($luna) {
                CasoRescate::create([
                    'id_mascota' => $luna->id_mascota,
                    'situacion' => 'Encontrada en la calle con infección ocular grave.',
                    'historia' => 'Luna fue encontrada vagando con una infección ocular tan severa que estuvo a punto de perder la vista. Gracias a un tratamiento intensivo, logró recuperarse casi completamente, aunque su ojo izquierdo quedó con visión limitada.',
                    'tratamiento' => 'Seguimiento oftalmológico mensual.',
                    'urgencia' => 'media',
                    'fecha_rescate' => '2024-01-20',
                ]);
            }

            if ($michi) {
                CasoRescate::create([
                    'id_mascota' => $michi->id_mascota,
                    'situacion' => 'Abandonado por supersticiones sobre gatos negros.',
                    'historia' => 'Michi fue abandonado en un basurero debido a creencias supersticiosas. Es un gato extremadamente cariñoso que solo busca amor y un hogar cálido.',
                    'tratamiento' => 'Desparasitación y vacunación completa.',
                    'urgencia' => 'alta',
                    'fecha_rescate' => '2024-03-10',
                ]);
            }

            if ($copito) {
                CasoRescate::create([
                    'id_mascota' => $copito->id_mascota,
                    'situacion' => 'Abandonado después de Semana Santa.',
                    'historia' => 'Copito fue un "regalo" de Semana Santa que terminó abandonado en un parque cuando la familia se cansó de él. Es tímido pero dulce cuando gana confianza.',
                    'tratamiento' => 'Dieta especial y socialización.',
                    'urgencia' => 'media',
                    'fecha_rescate' => '2024-03-15',
                ]);
            }
        }
    }
}
