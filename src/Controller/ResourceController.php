<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller;

use Optime\Acl\Bundle\Form\Type\ResourcesConfigType;
use Optime\Acl\Bundle\Service\Reference\UseCase\CleanReferencesUseCase;
use Optime\Acl\Bundle\Service\Reference\UseCase\SaveReferencesUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Manuel Aguirre
 */
#[Route("/resources")]
class ResourceController extends AbstractController
{
    public function __construct(
        private SaveReferencesUseCase $saveReferencesUseCase,
        private CleanReferencesUseCase $cleanReferencesUseCase,
    ) {
    }

    #[Route("/config/", name: "optime_acl_resources_config")]
    public function config(Request $request): Response
    {
        $persistedForm = $this->createResourcesForm($request,);
        $newsForm = $this->createResourcesForm($request, false);
        $hiddenForm = $this->createResourcesForm($request, hidden: true);

        foreach ([$persistedForm, $newsForm, $hiddenForm] as $form) {
            if ($form->isSubmitted() and $form->isValid()) {
                $this->saveReferencesUseCase->handle($form);
                $this->cleanReferencesUseCase->handle();

                $this->addFlash('success', 'Data saved successfully!');

                return $this->redirectToRoute('optime_acl_resources_config');
            }
        }

        return $this->renderForm('@OptimeAcl/resource/config.html.twig', [
            'persisted_form' => $persistedForm,
            'news_form' => $newsForm,
            'hidden_form' => $hiddenForm,
        ]);
    }

    #[Route("/clean/", name: "optime_acl_resources_clean")]
    public function clean(Request $request): Response
    {
        $this->cleanReferencesUseCase->handle();
        $this->addFlash('success', 'Data saved successfully!');

        return $this->redirect($request->headers->get(
            'referer',
            $this->generateUrl('optime_acl_resources_config')
        ));
    }

    private function createResourcesForm(
        Request $request,
        bool $persisted = true,
        bool $hidden = false
    ): FormInterface {
        $form = $this->createForm(ResourcesConfigType::class, null, [
            'persisted' => $persisted,
            'hidden' => $hidden,
        ]);
        $form->handleRequest($request);

        return $form;
    }
}