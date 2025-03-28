<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Screening;

interface ScreeningRepositoryInterface
{
    /**
     * Récupère toutes les séances
     */
    public function all(): Collection;

    /**
     * Récupère les séances paginées
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Trouve une séance par son ID
     */
    public function find(int $id): ?Screening;

    /**
     * Crée une nouvelle séance
     */
    public function create(array $data): Screening;

    /**
     * Met à jour une séance existante
     */
    public function update(int $id, array $data): bool;

    /**
     * Supprime une séance
     */
    public function delete(int $id): bool;

    /**
     * Filtre les séances par type (normal/vip)
     */
    public function filterByType(string $type): Collection;

    /**
     * Récupère les séances à venir pour un film
     */
    public function upcomingForMovie(int $movieId): Collection;

    /**
     * Récupère les séances pour une salle spécifique
     */
    public function findByHall(int $hallId): Collection;

    /**
     * Vérifie les conflits de réservation pour une salle
     */
    public function checkTimeConflict(int $hallId, string $startTime, string $endTime, ?int $ignoreId = null): bool;

    /**
     * Recherche des séances par critères
     */
    public function search(array $criteria): Collection;
}