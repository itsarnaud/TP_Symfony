<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Reservation;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\User;

class ReservationController extends AbstractController
{
    #[Route('/api/reservation', name: 'create_reservation', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createReservation(
        Request $req,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        ReservationRepository $reservationRepository
    ): JsonResponse 
    {
        $data = json_decode($req->getContent(), true);
        $reservation = new Reservation();
        $reservation->setDate(new \DateTime($data['date']));
        $reservation->setTimeSlot(new \DateInterval($data['timeSlot']));
        $reservation->setEventName($data['eventName']);
        $reservation->setRelations($this->getUser());

        //
        $now = new \DateTime();
        $reservationDate = $reservation->getDate();
        if ($reservationDate <= $now->modify('+24 hours')) {
            return new JsonResponse(['error' => 'Les réservations doivent se faire au moins 24 heures à l\'avance.'], 400);
        }

        //
        $existingReservation = $reservationRepository->findOneBy([
            'date' => $reservation->getDate(),
            'timeSlot' => $reservation->getTimeSlot()
        ]);

        if ($existingReservation) {
            return new JsonResponse(['error' => 'Cette plage horaire est déjà réservée pour cette date.'], 400);
        }

        //
        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $em->persist($reservation);
        $em->flush();

        return new JsonResponse(['message' => 'Réservation créée avec succès'], 201);
    }

    #[Route('/api/reservation', name: 'list_user_reservations', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function listUserReservations(ReservationRepository $reservationRepository, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        $reservations = $reservationRepository->findBy(['Relations' => $user]);
        $json = $serializer->serialize($reservations, 'json', ['groups' => 'reservation:read']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/admin/reservation', name: 'list_reservations', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listReservations(ReservationRepository $reservationRepository, SerializerInterface $serializer): JsonResponse
    {
        $reservations = $reservationRepository->findAll();
        $json = $serializer->serialize($reservations, 'json', ['groups' => 'reservation:read']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('api/admin/reservation/{id}', name: 'delete_reservation', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteReservation(Reservation $reservation, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($reservation);
        $em->flush();

        return new JsonResponse(['message' => 'Réservation supprimée avec succès'], 200);
    }

    #[Route('/api/admin/reservation/{id}', name: 'get_reservation_by_id', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getReservationById(Reservation $reservation, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($reservation, 'json', ['groups' => 'reservation:read']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/admin/reservation/{id}', name: 'update_reservation', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateReservation(Request $request, Reservation $reservation, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reservation->setDate(new \DateTime($data['date']));
        $reservation->setTimeSlot(new \DateInterval($data['timeSlot']));
        $reservation->setEventName($data['eventName']);

        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Réservation mise à jour avec succès'], 200);
    }

    #[Route('/api/admin/reservation', name: 'create_reservation_for_admin', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createReservationForAdmin(
        Request $req,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        ReservationRepository $reservationRepository
    ): JsonResponse 
    {
        $data = json_decode($req->getContent(), true);
        $reservation = new Reservation();
        $reservation->setDate(new \DateTime($data['date']));
        $reservation->setTimeSlot(new \DateInterval($data['timeSlot']));
        $reservation->setEventName($data['eventName']);
        $reservation->setRelations($em->getRepository(User::class)->find($data['user_id']));

        //
        $existingReservation = $reservationRepository->findOneBy([
            'date' => $reservation->getDate(),
            'timeSlot' => $reservation->getTimeSlot()
        ]);

        if ($existingReservation) {
            return new JsonResponse(['error' => 'Cette plage horaire est déjà réservée pour cette date.'], 400);
        }

        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $em->persist($reservation);
        $em->flush();

        return new JsonResponse(['message' => 'Réservation créée avec succès'], 201);
    }
}
