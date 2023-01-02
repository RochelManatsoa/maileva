<?php

namespace App\Controller;

use App\Form\ResiliationFormType;
use App\Manager\ResiliationManager;
use App\Repository\LetterRepository;
use App\Repository\ServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\{Letter, Service, Category, Resiliation};
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResiliationController extends AbstractController
{
    /**
     * @Route("/resiliation/{slug}", name="app_resiliation_category")
     */
    public function category(
        Request $request,
        Category $category,
        LetterRepository $letterRepository,
        ServiceRepository $serviceRepository,
        ResiliationManager $resiliationManager
    ) {
        $service = $serviceRepository->find($category);
        $services = $serviceRepository->findAll($category);
        $dataServices = $this->objectToArray($services);
        $models = $this->objectToArray($letterRepository->findAll());
        $resiliation = $resiliationManager->init();
        $resiliation->setService($service);
        $form = $this->createForm(ResiliationFormType::class, $resiliation, [
            'defaultModel' => null,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resiliation = $form->getData();
            $resiliationManager->save($resiliation);

            return $this->redirectToRoute('app_resiliation_resume', [
                'customId' => $resiliation->getCustomId(),
            ]);
        }

        return $this->render('resiliation/service.html.twig', [
            'services' => $services,
            'dataServices' => $dataServices,
            'letters' => $models,
            'category' => $category->getName(),
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/resiliation2/{slug}", name="app_resiliation2_category")
     */
    public function category2(
        Request $request,
        Category $category,
        LetterRepository $letterRepository,
        ServiceRepository $serviceRepository,
        ResiliationManager $resiliationManager
    ) {
        $service = $serviceRepository->find($category);
        $services = $serviceRepository->findAll($category);
        $dataServices = $this->objectToArray($services);
        $models = $this->objectToArray($letterRepository->findAll());
        $resiliation = $resiliationManager->init();
        $resiliation->setService($service);
        $form = $this->createForm(ResiliationFormType::class, $resiliation, [
            'defaultModel' => null,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resiliation = $form->getData();
            $resiliationManager->save($resiliation);

            return $this->redirectToRoute('app_resiliation2_resume', [
                'customId' => $resiliation->getCustomId(),
            ]);
        }

        return $this->render('resiliation/service2.html.twig', [
            'services' => $services,
            'dataServices' => $dataServices,
            'letters' => $models,
            'category' => $category->getName(),
            'form' => $form->createView(),
            'template' => 'design2',
        ]);
    }


    /**
     * Array of Object to array
     */
    private function objectToArray(array $array)
    {
        $newArray = [];

        foreach ($array as $key => $object) {
            if ($object instanceof Letter) {
                $newArray[$key + 1] = [
                    'content' => $object->getContent(),
                ];
            }

            if ($object instanceof Service) {
                $newArray[$object->getSlug()] = [
                    'address' => $object->getAddress(),
                    'complement' => $object->getComplement(),
                    'zipCode' => $object->getZipCode(),
                    'city' => $object->getCity(),
                    'name' => $object->getName(),
                ];
            }
        }

        return $newArray;
    }

    /**
     * @Route("/preview/{customId}", name="app_resiliation_preview")
     */
    public function preview(
        Resiliation $resiliation,
        Request $request,
        LetterRepository $letterRepository,
        ServiceRepository $serviceRepository,
        ResiliationManager $resiliationManager
    ) {
        $services = $serviceRepository->findAll(
            $resiliation->getService()->getCategory()
        );
        $dataServices = $this->objectToArray($services);
        $defaultModel = $letterRepository->findOneByName(
            $resiliation->getType()
        );
        $models = $this->objectToArray($letterRepository->findAll());
        $form = $this->createForm(ResiliationFormType::class, $resiliation, [
            'defaultModel' => $defaultModel,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resiliation = $form->getData();
            $resiliationManager->save($resiliation);

            return $this->redirectToRoute('app_resiliation_resume', [
                'customId' => $resiliation->getCustomId(),
            ]);
        }

        return $this->render('resiliation/service.html.twig', [
            'services' => $services,
            'dataServices' => $dataServices,
            'letters' => $models,
            'category' => $resiliation
                ->getService()
                ->getCategory()
                ->getName(),
            'form' => $form->createView(),
        ]);
    }

    
    /**
     * @Route("/preview2/{customId}", name="app_resiliation2_preview")
     */
    public function preview2(
        Resiliation $resiliation,
        Request $request,
        LetterRepository $letterRepository,
        ServiceRepository $serviceRepository,
        ResiliationManager $resiliationManager
    ) {
        $services = $serviceRepository->findAll(
            $resiliation->getService()->getCategory()
        );
        $dataServices = $this->objectToArray($services);
        $defaultModel = $letterRepository->findOneByName(
            $resiliation->getType()
        );
        $models = $this->objectToArray($letterRepository->findAll());
        $form = $this->createForm(ResiliationFormType::class, $resiliation, [
            'defaultModel' => $defaultModel,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resiliation = $form->getData();
            $resiliationManager->save($resiliation);

            return $this->redirectToRoute('app_resiliation2_resume', [
                'customId' => $resiliation->getCustomId(),
            ]);
        }

        return $this->render('resiliation/service2.html.twig', [
            'services' => $services,
            'dataServices' => $dataServices,
            'letters' => $models,
            'category' => $resiliation
                ->getService()
                ->getCategory()
                ->getName(),
            'form' => $form->createView(),
            'template' => 'design2'
        ]);
    }


    /**
     * @Route("/resume/{customId}", name="app_resiliation_resume")
     */
    public function resume(Resiliation $resiliation)
    {
        return $this->render('resiliation/recap.html.twig', [
            'category' => $resiliation
                ->getService()
                ->getCategory()
                ->getName(),
            'resiliation' => $resiliation,
        ]);
    }

    /**
     * @Route("/resume2/{customId}", name="app_resiliation2_resume")
     */
    public function resume2(Resiliation $resiliation)
    {
        return $this->render('resiliation/recap2.html.twig', [
            'category' => $resiliation
                ->getService()
                ->getCategory()
                ->getName(),
            'resiliation' => $resiliation,
            'template' => 'design2',
        ]);
    }

    /**
     * @Route("/apercu-pdf/{customId}", name="app_resiliation_preview_doc")
     */
    public function previewDocument(
        Resiliation $resiliation,
        ResiliationManager $resiliationManager
    ) {
        $file = $resiliationManager->generatePreview($resiliation);

        //return new BinaryFileResponse($file);
        return $this->render('resiliation/pdf/preview.pdf.twig',[
            'resiliation' => $resiliation
        ]);
    }
}
