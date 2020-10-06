<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\ConfirmationType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 *
 * @Route ("/admin/categorie")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route ("/")
     */
    public function index(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render(
            'admin/category/index.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * l'id est optionnel et vaut null par défaut :
     * si on ne passe pas d'id dans l'url, on est en création,
     * si on passe un id, on est en modification
     * @Route ("/edition/{id}", defaults={"id": null})
     * EntityManagerInterface $entityManager permet d'enregistrement en bdd
     */
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        $id
    ) {
        if(is_null($id)) { // création
            $category = new Category();
        } else { // modification
            $category = $categoryRepository->find($id);
        }


        // création du formulaire relié à la catégorie
        $form = $this->createForm(CategoryType::class, $category);

        // le formulaire analyse la requetr
        // et sette les valeurs des attributs Category avec les valeurs
        // avec les valeurs saisies dans le formulaire s'il a été anvoyé
        $form->handleRequest($request); // handles analyse la requete

        dump($category);

        // si le formulaire a été soumis
        if ($form->isSubmitted()) {
            // si les validations à partir des annotation @Assert
            // dans l'entité Category sont ok
            if($form->isValid()) {
                // quand on va appeler la méthode flush,
                // la catégorie devra être enregistrée en bdd
                $entityManager->persist($category); // enregistrement en bdd 1er étape (mijery ny erreur reetra)
                // enregistrement en bdd
                $entityManager->flush(); // enregistrement en bdd 2e étape (manao ny enregistre @ izay)

                // enregistrement dans la session d'un message pour affichage unique
                $this->addFlash('success', 'La catégorie est enregistrée');

                // redirection vers la page de liste
                return $this->redirectToRoute('app_admin_category_index');
            }

        }

        return $this->render(
            'admin/category/edit.html.twig',
            [
                // pour pouvoir utiliser le formulaire dans le template
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route ("/suppression/{id}", name="admin_category_delete")
     * Le ParamConverter (installé grace a sensio/framework-extra-bundle)
     * permet de convertir las paramètres des routes
     * Ici, il va rechercher la Category en fonction de l'id présent dans l'adresse
     */
    public function delete(Category $category, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(ConfirmationType::class);
        $form->handleRequest($request); // envoie la requete

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($category); // préparation pour supprimer
            $em->flush(); // execution

            // affichage du message flash
            $this->addFlash('info', 'La catégorie ' . $category->getName() . ' a été supprimée.');

            // retourner car l'id n'existe plus
            return  $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/delete.html.twig',
        [
            'delete_form' => $form->createView(),
            'category' => $category,
        ]);
    }
}