<?php

namespace App\Controller;

use App\Entity\AdviceRequest;
use App\Entity\Patient;
use App\Entity\User;
use App\Form\AdviceRequestType;
use App\Repository\AdviceRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/ask")
 */
class AdviceRequestController extends AbstractController
{

    /**
     * @Route("/", name="advice_request_index", methods={"GET"})
     */
    public function index(AdviceRequestRepository $adviceRequestRepository): Response
    {
        $userRole = $this->getUser()->getRoles();

        if (in_array("ROLE_DOCTOR", $userRole)) {
            return $this->redirectToRoute('advice_request_show');
        }
        if (in_array("ROLE_PATIENT", $userRole)) {
            return $this->redirectToRoute('advice_request_new');
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/new", name="advice_request_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $adviceRequest = new AdviceRequest();
        $form = $this->createForm(AdviceRequestType::class, $adviceRequest);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user =$this->getUser();
            $patient=$user->getPatient();
            $adviceRequest->setPatient($patient);
            $entityManager->persist($adviceRequest);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('advice_request/new.html.twig', [
            'advice_request' => $adviceRequest,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show", name="advice_request_show", methods={"GET"})
     */
    public function show(AdviceRequestRepository $adviceRequestRepository): Response
    {
        $user = $this->getUser();
        $doctor= $user->getDoctor();
        $request = $adviceRequestRepository->findBy([
            'pathology' => $doctor->getSpeciality()
        ]);
        return $this->render('advice_request/show.html.twig', [
            'advice_request' => $request,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="advice_request_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AdviceRequest $adviceRequest): Response
    {
        $form = $this->createForm(AdviceRequestType::class, $adviceRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advice_request_index');
        }

        return $this->render('advice_request/edit.html.twig', [
            'advice_request' => $adviceRequest,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advice_request_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AdviceRequest $adviceRequest): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adviceRequest->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($adviceRequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('advice_request_index');
    }
}
