<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use App\Entity\GithubRepo;

class GithubRepoController extends AbstractController
{
    /**
     * @Route("/", name="github_repo_list")
     */
    public function index(Request $request)
    {
        // Create a form to allow user to fetch new data from the GitHub API.
        $form = $this->createFormBuilder(null, ['attr' => ['class' => 'form-inline']])
            ->add('count', IntegerType::class, [
                'label' => FALSE,
                'attr' => [
                    'title' => 'Enter a number between 0 and 100.',
                    'class' => 'mr-1',
                ]])
            ->add('go', SubmitType::class, ['label' => 'Go'])
            ->getForm();

        // If the form was just submitted, process the input and fetch new data.
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $input = $form->getData();
            if (isset($input['count']) && is_int($input['count']) && ($input['count'] >= 0) && ($input['count'] <= 100))
            {
                $repos = $this->fetch($input['count']);
                if (FALSE === $repos)
                {
                    // There was a problem fetching repos from the GitHub API. Set message letting user know.
                    $this->addFlash('error', 'There was a problem fetching data from the GitHub API. Please try again.');
                } else {
                    // Update database with fetched $repos.
                    $this->save($repos);
                    // Redirect to avoid "resubmit form" errors if user refreshes page.
                    return $this->redirectToRoute('github_repo_list');
                }
            } else {
                // Set error message to indicate that count must be between 0 and 100.
                $this->addFlash('error', 'The number of repos to fetch must be between 0 and 100.');
            }
        }

        // Get the list of repositories from the database.
        $repos = $this->loadReposFromDb();

        // Render the Twig template with the form and list of repositories.
        return $this->render('github_repo/index.html.twig', [
            'form' => $form->createView(),
            'repos' => $repos,
        ]);
    }

    /**
     * @Route("/repo/{repository_id}", name="github_repo_detail")
     */
    public function show(GithubRepo $repo)
    {
        return $this->render('github_repo/detail.html.twig', [
            'repo' => $repo,
        ]);
    }


    /**
     * Get all repos stored in the database, keyed by repository_id and sorted by stars descending.
     * 
     * @return GithubRepo[]
     */
    protected function loadReposFromDb()
    {
        return $this->getDoctrine()
            ->getRepository(GithubRepo::class)
            ->findAllKeyedByRepositoryId();
    }


    /**
     * Fetch the specified number of public PHP repos with the top number of stars from the GitHub API.
     * 
     * @param int $count The number of repos to fetch from the GitHub API.
     * 
     * @return GithubRepo[]|bool Array of fetched GithubRepo objects, or FALSE if unable to fetch repos.
     */
    protected function fetch(int $count)
    {
        // Make sure $count is within acceptable range of 0 to 100.
        if (($count < 0) || ($count > 100))
        {
            // $count is outside of acceptable range.
            return FALSE;
        } elseif ($count === 0) {
            // Return an empty array.
            return [];
        }
        // Set $repos to FALSE so that it will be the default return value in case repos cannot be fetched.
        $repos = FALSE;
        // Build the url query for the GitHub API.
        $query = [
          // Get public PHP repos.
          'q=language:php+is:public',
          // Get repos with the most stars.
          'sort=stars',
          'order=desc',
          // Get the specified number of repos.
          "per_page=$count",
        ];
        // Build the final $endpoint url.
        $endpoint = 'https://api.github.com/search/repositories?' . implode('&', $query);
        // Use try...catch exception handling in case of error while fetching data from API.
        try
        {
            // Use Symfony's HttpClient to fetch data from the API.
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $endpoint, [
                'headers' => [
                    'Accept' => 'application/vnd.github.v3+json',
                ],
            ]);
        } catch(Exception $e) {
            // An error occurred fetching repos from the GitHub API. Set $response to FALSE.
            $response = FALSE;
        }
        // If API call was successful, continue processing fetched repos.
        if ((FALSE !== $response) && (200 === $response->getStatusCode()))
        {
            $repos = [];
            $content = $response->getContent();
            $data = json_decode($content, TRUE);
            if (!empty($data['items']))
            {
                // Properties that are required to save a repo to the database.
                $required_properties = ['id','name','html_url','created_at','pushed_at','stargazers_count'];
                // Loop through the fetched items to create GithubRepo objects.
                foreach ($data['items'] as $item)
                {
                    // Check to make sure the item has the required properties.
                    $valid_item = TRUE;
                    foreach ($required_properties as $property)
                    {
                        if (empty($item[$property]))
                        {
                            $valid_item = FALSE;
                            break;
                        }
                    }
                    // If the item has all required properties, create a GithubRepo object.
                    if ($valid_item)
                    {
                        // For GitHub repos that do not have a description, set description to an empty string.
                        $description = !empty($item['description']) ? (string)$item['description'] : '';
                        // Create GithubRepo object.
                        $repo = new GithubRepo();
                        $repo->setRepositoryId((int)$item['id'])
                            ->setName((string)$item['name'])
                            ->setUrl((string)$item['html_url'])
                            ->setCreatedDate((string)$item['created_at'])
                            ->setLastPushDate((string)$item['pushed_at'])
                            ->setDescription($description)
                            ->setStars((int)$item['stargazers_count']);
                        //Add $repo to $repos array, keyed by repository_id.
                        $repos[(int)$item['id']] = $repo;
                    }
                }
                // Set message letting user know fetch was successful.
                $this->addFlash('success', "Successfully fetched the top $count PHP repositories from the GitHub API!");
            }
        }
        return $repos;
    }


    /**
     * Save repos to the database.
     * 
     * @param array $repos Array of GithubRepo objects to be saved to the database.
     */
    protected function save(array $repos)
    {
        // Get array of repos current stored in the database.
        $existing = $this->loadReposFromDb();

        // Use Doctrine to save repo entities to the database.
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($repos as $repo_id => $repo)
        {
            // Make sure $repo is an instance of GithubRepo.
            if ($repo instanceof GithubRepo)
            {
                // Check if this repo already exists in the database.
                if (!empty($existing[$repo_id]))
                {
                    // This repo already exists in the database.
                    // Check if the database record needs to be updated.
                    $repo->setId($existing[$repo_id]->getId());
                    if ($repo != $existing[$repo_id])
                    {
                        // There are differences that need to be saved to the database.
                        $entityManager->persist($repo);
                    }
                } else {
                    // This repo does not yet exist in the database, therefore save it.
                    $entityManager->persist($repo);
                }
            } else {
                // This is not an instance of GithubRepo. Remove it from $repos array.
                unset($repos[$repo_id]);
            }
        }

        // Flush $entityManager to actually save entities to the database.
        $entityManager->flush();

        // Delete repos that are currently stored in the database but are not included in the data fetched from the API.
        $existing_ids = array_keys($existing);
        $new_ids = array_keys($repos);
        $delete_ids = array_diff($existing_ids, $new_ids);
        return $this->getDoctrine()
            ->getRepository(GithubRepo::class)
            ->deleteByRepositoryId($delete_ids);
    }

}
