@extends('layouts.admin')

@section('title', 'Test CashPay Integration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card mr-2"></i>
                        Test d'intégration CashPay V2.0
                    </h3>
                </div>
                
                <div class="card-body">
                    <!-- Statut de l'intégration -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Statut de l'intégration</h5>
                                </div>
                                <div class="card-body">
                                    <div id="integration-status" class="text-center">
                                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                        <p class="mt-2">Vérification en cours...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <div id="config-display">
                                        <i class="fas fa-spinner fa-spin"></i> Chargement...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions de test -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Actions de test</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group flex-wrap" role="group">
                                        <button type="button" class="btn btn-primary" onclick="testAuthentication()">
                                            <i class="fas fa-key"></i> Test Authentification
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="getGateways()">
                                            <i class="fas fa-gateway"></i> Récupérer Passerelles
                                        </button>
                                        <button type="button" class="btn btn-info" onclick="createLedger()">
                                            <i class="fas fa-plus"></i> Créer Ledger
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="listLedgers()">
                                            <i class="fas fa-list"></i> Lister Ledgers
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Résultats des tests -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Résultats des tests</h5>
                                    <button type="button" class="btn btn-sm btn-outline-secondary float-right" onclick="clearResults()">
                                        Effacer
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="test-results" style="max-height: 400px; overflow-y: auto;">
                                        <p class="text-muted">Les résultats des tests s'afficheront ici...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ledgers actifs -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Ledgers actifs</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary float-right" onclick="refreshLedgers()">
                                        <i class="fas fa-refresh"></i> Actualiser
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="ledgers-list">
                                        <p class="text-muted">Cliquez sur "Actualiser" pour voir les ledgers...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.test-result {
    border-left: 4px solid #ddd;
    padding: 10px;
    margin-bottom: 10px;
    background-color: #f8f9fa;
}
.test-result.success {
    border-left-color: #28a745;
    background-color: #d4edda;
}
.test-result.error {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}
.test-result.warning {
    border-left-color: #ffc107;
    background-color: #fff3cd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    checkIntegrationStatus();
    loadConfiguration();
});

function checkIntegrationStatus() {
    fetch('{{ route('admin.cashpay.test') }}')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('integration-status');
            if (data.success) {
                statusDiv.innerHTML = `
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                    <p class="mt-2 text-success font-weight-bold">CashPay fonctionnel</p>
                    <small class="text-muted">${data.message}</small>
                `;
            } else {
                statusDiv.innerHTML = `
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                    <p class="mt-2 text-danger font-weight-bold">Problème détecté</p>
                    <small class="text-muted">${data.message}</small>
                `;
            }
        })
        .catch(error => {
            const statusDiv = document.getElementById('integration-status');
            statusDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                <p class="mt-2 text-warning font-weight-bold">Erreur de test</p>
                <small class="text-muted">Impossible de tester la connexion</small>
            `;
        });
}

function loadConfiguration() {
    fetch('{{ route('admin.cashpay.config') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const config = data.config;
                const configHtml = `
                    <div class="row">
                        <div class="col-6"><strong>URL Base:</strong></div>
                        <div class="col-6"><code>${config.base_url}</code></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Username:</strong></div>
                        <div class="col-6"><code>${config.username}</code></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Client ID:</strong></div>
                        <div class="col-6"><code>${config.client_id}</code></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>API Reference:</strong></div>
                        <div class="col-6"><code>${config.api_reference}</code></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Currency:</strong></div>
                        <div class="col-6"><code>${config.currency}</code></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><strong>Terminal:</strong></div>
                        <div class="col-6"><code>${config.terminal_code}</code></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <span class="badge ${config.has_password ? 'badge-success' : 'badge-danger'}">
                                Password ${config.has_password ? 'configuré' : 'manquant'}
                            </span>
                            <span class="badge ${config.has_client_secret ? 'badge-success' : 'badge-danger'}">
                                Client Secret ${config.has_client_secret ? 'configuré' : 'manquant'}
                            </span>
                            <span class="badge ${config.has_api_key ? 'badge-success' : 'badge-danger'}">
                                API Key ${config.has_api_key ? 'configurée' : 'manquante'}
                            </span>
                        </div>
                    </div>
                `;
                document.getElementById('config-display').innerHTML = configHtml;
            }
        })
        .catch(error => {
            document.getElementById('config-display').innerHTML = '<p class="text-danger">Erreur chargement configuration</p>';
        });
}

function testAuthentication() {
    addTestResult('Test d\'authentification en cours...', 'info');
    
    fetch('{{ route('admin.cashpay.test') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addTestResult(`✅ Authentification réussie: ${data.message}`, 'success');
                if (data.data) {
                    addTestResult(`Données reçues: ${JSON.stringify(data.data, null, 2)}`, 'info');
                }
            } else {
                addTestResult(`❌ Échec authentification: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            addTestResult(`❌ Erreur test authentification: ${error.message}`, 'error');
        });
}

function getGateways() {
    addTestResult('Récupération des passerelles en cours...', 'info');
    
    fetch('{{ route('admin.cashpay.gateways') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.gateways) {
                addTestResult(`✅ ${data.gateways.length} passerelle(s) trouvée(s)`, 'success');
                data.gateways.forEach((gateway, index) => {
                    addTestResult(`Passerelle ${index + 1}: ${JSON.stringify(gateway, null, 2)}`, 'info');
                });
            } else {
                addTestResult(`❌ Erreur récupération passerelles: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            addTestResult(`❌ Erreur récupération passerelles: ${error.message}`, 'error');
        });
}

function createLedger() {
    addTestResult('Création d\'un nouveau ledger...', 'info');
    
    fetch('{{ route('admin.cashpay.create-ledger') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addTestResult(`✅ Ledger créé avec succès: ${data.ledger_reference}`, 'success');
                if (data.data) {
                    addTestResult(`Détails: ${JSON.stringify(data.data, null, 2)}`, 'info');
                }
                refreshLedgers();
            } else {
                addTestResult(`❌ Erreur création ledger: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            addTestResult(`❌ Erreur création ledger: ${error.message}`, 'error');
        });
}

function listLedgers() {
    refreshLedgers();
}

function refreshLedgers() {
    const ledgersDiv = document.getElementById('ledgers-list');
    ledgersDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement des ledgers...';
    
    fetch('{{ route('admin.cashpay.ledgers') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.ledgers) {
                if (data.ledgers.length === 0) {
                    ledgersDiv.innerHTML = '<p class="text-muted">Aucun ledger trouvé.</p>';
                } else {
                    let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Référence</th><th>État</th><th>Créé le</th><th>Factures</th><th>Actions</th></tr></thead><tbody>';
                    
                    data.ledgers.forEach(ledger => {
                        html += `
                            <tr>
                                <td><code>${ledger.reference}</code></td>
                                <td>
                                    <span class="badge ${ledger.state === 0 ? 'badge-success' : 'badge-secondary'}">
                                        ${ledger.state === 0 ? 'Ouvert' : 'Fermé'}
                                    </span>
                                </td>
                                <td>${new Date(ledger.createdAt).toLocaleString()}</td>
                                <td>${ledger.nbInvoices || 0}</td>
                                <td>
                                    ${ledger.state === 0 ? 
                                        `<button class="btn btn-sm btn-warning" onclick="closeLedger('${ledger.reference}')">Fermer</button>` : 
                                        '<span class="text-muted">Fermé</span>'
                                    }
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += '</tbody></table></div>';
                    ledgersDiv.innerHTML = html;
                }
                addTestResult(`✅ ${data.ledgers.length} ledger(s) récupéré(s)`, 'success');
            } else {
                ledgersDiv.innerHTML = '<p class="text-danger">Erreur chargement des ledgers</p>';
                addTestResult(`❌ Erreur récupération ledgers: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            ledgersDiv.innerHTML = '<p class="text-danger">Erreur chargement des ledgers</p>';
            addTestResult(`❌ Erreur récupération ledgers: ${error.message}`, 'error');
        });
}

function closeLedger(reference) {
    if (!confirm('Êtes-vous sûr de vouloir fermer ce ledger ?')) {
        return;
    }
    
    addTestResult(`Fermeture du ledger ${reference}...`, 'info');
    
    fetch(`{{ route('admin.cashpay.close-ledger', ':reference') }}`.replace(':reference', reference), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addTestResult(`✅ Ledger fermé: ${data.message}`, 'success');
                refreshLedgers();
            } else {
                addTestResult(`❌ Erreur fermeture ledger: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            addTestResult(`❌ Erreur fermeture ledger: ${error.message}`, 'error');
        });
}

function addTestResult(message, type = 'info') {
    const resultsDiv = document.getElementById('test-results');
    const timestamp = new Date().toLocaleTimeString();
    
    const resultElement = document.createElement('div');
    resultElement.className = `test-result ${type}`;
    resultElement.innerHTML = `
        <strong>[${timestamp}]</strong> ${message}
    `;
    
    resultsDiv.appendChild(resultElement);
    resultsDiv.scrollTop = resultsDiv.scrollHeight;
}

function clearResults() {
    document.getElementById('test-results').innerHTML = '<p class="text-muted">Les résultats des tests s\'afficheront ici...</p>';
}
</script>
@endsection
