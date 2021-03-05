<?php
namespace App\Service;

use App\Entity\ReviewsFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReviewsService extends AbstractController
{
    private $reviewsUrl;
    private $reviewsAuth;

    public function __construct(string $reviewsUrl, string $reviewsAuth)
    {
        $this->reviewsUrl = $reviewsUrl;
        $this->reviewsAuth = $reviewsAuth;
    }

    public function fetchReviews()
    {
        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_URL, $this->reviewsUrl);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->reviewsAuth
        ));
    
        $response = curl_exec($cURLConnection);
        curl_close($cURLConnection);
    
        return json_decode($response)->reviews;
    }

    public function sortReviews($reviews, $orderRating, $orderDate)
    {
        $filteredReviews = [];
        $ratingOptions = ['1', '2', '3', '4', '5'];
        foreach ($ratingOptions as $key => $option) {
            $filteredReviews[$option] = [];
        }

        if ($orderRating) {
            $filteredReviews = array_reverse($filteredReviews, true);
        }
        foreach ($reviews as $review) {
            array_push($filteredReviews[$review->rating], $review);
        }

        foreach ($filteredReviews as $key => $review) {
            usort($filteredReviews[$key], function($a, $b) use ($orderDate) {
                if ($orderDate) {
                    return strtotime($b->reviewCreatedOnDate) - strtotime($a->reviewCreatedOnDate);
                }
                return strtotime($a->reviewCreatedOnDate) - strtotime($b->reviewCreatedOnDate);
            });
        }

        $result = [];
        foreach ($filteredReviews as $key => $review) {
            foreach ($review as $index => $value) {
                array_push($result, $review[$index]);
            }
        }

        return $result;
    }

    public function createReviewsFilterForm()
    {
        $filter = new ReviewsFilter();

        $form = $this->createFormBuilder($filter)
            ->add('orderRating', ChoiceType::class, [
                'choices' => [
                    'Highest First' => true,
                    'Lowest First' => false
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('minRating', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('orderDate', ChoiceType::class, [
                'choices' => [
                    'Newest First' => true,
                    'Oldest First' => false
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('textPriority', ChoiceType::class, [
                'choices' => [
                    'Yes' => true,
                    'No' => false
                ],
                'expanded' => false,
                'multiple' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Apply filter',
                'attr' => [
                    'class' => 'float-right btn-primary'
                ]
            ]);
            
        return $form->getForm();
    }
}