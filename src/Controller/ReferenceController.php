<?php
/**
 * @author Manuel Aguirre
 */

declare(strict_types=1);

namespace Optime\Acl\Bundle\Controller;

use Optime\Acl\Bundle\Form\Type\Config\ReferencesConfigType;
use Optime\Acl\Bundle\Service\Reference\UseCase\SaveReferencesUseCase;
use Optime\Acl\Bundle\Service\Resource\UseCase\CleanResourcesUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Manuel Aguirre
 */
#[Route("/references")]
class ReferenceController extends AbstractController
{
    public function __construct(
        private SaveReferencesUseCase $saveReferencesUseCase,
        private CleanResourcesUseCase $cleanResourcesUseCase,
    ) {
    }

    #[Route("/", name: "optime_acl_references_config")]
    public function config(Request $request): Response
    {
        $persistedForm = $this->createResourcesForm($request,);
        $newsForm = $this->createResourcesForm($request, false);
        $hiddenForm = $this->createResourcesForm($request, hidden: true);

        foreach ([$persistedForm, $newsForm, $hiddenForm] as $form) {
            if ($form->isSubmitted() and $form->isValid()) {
                $this->saveReferencesUseCase->handle($form);
                $this->cleanResourcesUseCase->handle();

                $this->addFlash('success', 'Data saved successfully!');

                return $this->redirectToRoute('optime_acl_references_config');
            }
        }

        return $this->renderForm('@OptimeAcl/reference/config.html.twig', [
            'persisted_form' => $persistedForm,
            'news_form' => $newsForm,
            'hidden_form' => $hiddenForm,
        ]);
    }

    private function createResourcesForm(
        Request $request,
        bool $persisted = true,
        bool $hidden = false
    ): FormInterface {
        $form = $this->createForm(ReferencesConfigType::class, null, [
            'persisted' => $persisted,
            'hidden' => $hidden,
        ]);
        $form->handleRequest($request);

        return $form;
    }
}