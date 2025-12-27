<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $empresa->razon_social ?? config('app.name') }} - Laboratorio Clínico</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700" rel="stylesheet" />

        <!-- Bootstrap 5.3 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

        <style>
            * {
                font-family: 'Poppins', sans-serif;
            }
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }
            .hero-section {
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.2);
                overflow: hidden;
            }
            .feature-card {
                background: white;
                border-radius: 15px;
                padding: 30px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                height: 100%;
            }
            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            }
            .feature-icon {
                width: 70px;
                height: 70px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
                color: white;
                margin: 0 auto 20px;
            }
            .btn-lab-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                padding: 15px 40px;
                font-size: 18px;
                font-weight: 600;
                border-radius: 50px;
                color: white;
                transition: all 0.3s ease;
            }
            .btn-lab-primary:hover {
                transform: scale(1.05);
                box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
                color: white;
            }
            .stat-number {
                font-size: 48px;
                font-weight: 700;
                color: white;
            }
            .logo-container {
                width: 150px;
                height: 150px;
                border-radius: 50%;
                background: white;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">
                    <i class="bi bi-heart-pulse-fill me-2"></i>
                    {{ $empresa->razon_social ?? 'Laboratorio Clínico' }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @if (Route::has('login'))
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/dashboard') }}">
                                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                                    </a>
                                </li>
                            @endauth
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="container py-5">
            <div class="hero-section my-5">
                <div class="row g-0">
                    <div class="col-lg-6 p-5 d-flex flex-column justify-content-center">
                        @if($empresa && $empresa->logo)
                            <div class="logo-container mb-4">
                                <img src="{{ asset('storage/' . $empresa->logo) }}" alt="{{ $empresa->razon_social }}" class="img-fluid" style="max-width: 120px; max-height: 120px; object-fit: contain;">
                            </div>
                        @else
                            <div class="logo-container mb-4">
                                <i class="bi bi-hospital-fill" style="font-size: 80px; color: #667eea;"></i>
                            </div>
                        @endif
                        <h1 class="display-4 fw-bold mb-3" style="color: #2d3748;">
                            {{ $empresa->razon_social ?? 'Laboratorio Clínico' }}
                        </h1>
                        <p class="lead mb-4" style="color: #718096;">
                            Tecnología de punta al servicio de su salud. Resultados confiables y precisos para un diagnóstico certero.
                        </p>
                        @if($empresa)
                            <div class="mb-4">
                                <p class="mb-2"><i class="bi bi-geo-alt-fill text-primary me-2"></i> {{ $empresa->direccion }}, {{ $empresa->ciudad }}</p>
                                <p class="mb-2"><i class="bi bi-telephone-fill text-primary me-2"></i> {{ $empresa->telefono_uno }}</p>
                                <p class="mb-2"><i class="bi bi-envelope-fill text-primary me-2"></i> {{ $empresa->email }}</p>
                                @if($empresa->nit)
                                    <p class="mb-0"><i class="bi bi-card-text text-primary me-2"></i> NIT: {{ $empresa->nit }}</p>
                                @endif
                            </div>
                        @endif
                        <div>
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-lab-primary">
                                    <i class="bi bi-speedometer2 me-2"></i> Ir al Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-lab-primary">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Acceso al Sistema
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="col-lg-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 60px 40px;">
                        <h2 class="text-white fw-bold mb-4 text-center">Estadísticas</h2>
                        <div class="row text-center text-white">
                            <div class="col-6 mb-4">
                                <div class="stat-number">{{ \App\Models\Cliente::count() }}</div>
                                <p class="mb-0 fw-500">Pacientes Registrados</p>
                            </div>
                            <div class="col-6 mb-4">
                                <div class="stat-number">{{ \App\Models\Examen::where('status', 1)->count() }}</div>
                                <p class="mb-0 fw-500">Exámenes Disponibles</p>
                            </div>
                            <div class="col-6">
                                <div class="stat-number">{{ \App\Models\Profesional::where('status', 1)->count() }}</div>
                                <p class="mb-0 fw-500">Profesionales</p>
                            </div>
                            <div class="col-6">
                                <div class="stat-number">24/7</div>
                                <p class="mb-0 fw-500">Atención Disponible</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <h2 class="text-center fw-bold text-white mb-5">Nuestros Servicios</h2>
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Resultados Confiables</h4>
                        <p class="text-muted">Equipos de última generación y personal altamente calificado para garantizar la precisión de sus análisis.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Entrega Rápida</h4>
                        <p class="text-muted">Procesamiento ágil de muestras y entrega oportuna de resultados para su tranquilidad.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-person-hearts"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Atención Personalizada</h4>
                        <p class="text-muted">Trato humano y profesional en cada etapa del proceso, desde la toma de muestra hasta la entrega de resultados.</p>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Acreditación</h4>
                        <p class="text-muted">Cumplimos con los más altos estándares de calidad y normativas vigentes en el sector salud.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-lightning"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Tecnología Avanzada</h4>
                        <p class="text-muted">Equipos automatizados de última generación para análisis precisos y confiables.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-file-earmark-medical"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Amplio Portafolio</h4>
                        <p class="text-muted">Más de {{ \App\Models\Examen::where('status', 1)->count() }} tipos de exámenes especializados disponibles.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-white text-center py-4" style="background: rgba(0,0,0,0.2);">
            <div class="container">
                <p class="mb-0">&copy; {{ date('Y') }} {{ $empresa->razon_social ?? 'Laboratorio Clínico' }}. Todos los derechos reservados.</p>
            </div>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
