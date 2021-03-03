<?php
namespace App\Controller;

use App\Entity\ReviewsFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReviewsController extends AbstractController
{
    private function fetchReviews()
    {
        $url = getenv('REVIEWS_URL');

        $cURLConnection = curl_init();
    
        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . getenv('REVIEWS_AUTH')
        ));
    
        $response = curl_exec($cURLConnection);
        curl_close($cURLConnection);
    
        return json_decode($response)->reviews;
    }

    private function sortReviews($reviews, $orderRating, $orderDate)
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

    public function index(Request $request)
    {
        $data = [];
        $reviews = $this->fetchReviews();
        
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
            ])
            ->getForm();

        $form->handleRequest($request);
        $minRating;

        if ($form->isSubmitted() && $form->isValid()) {
            $filter = $form->getData();
            $minRating = $filter->getMinRating();
            $orderDate = $filter->getOrderDate();
            $orderRating = $filter->getOrderRating();
            $textPriority = $filter->getTextPriority();

            $filteredArray = [];
            foreach ($reviews as $review) {
                if ($review->rating >= $minRating) {
                    array_push($filteredArray, $review);
                }
            }
            $reviews = $filteredArray;

            if ($textPriority) {
                $reviewsWithText = [];
                $reviewsWithoutText = [];
                foreach ($reviews as $review) {
                    $review->reviewText ? array_push($reviewsWithText, $review) : array_push($reviewsWithoutText, $review);
                }

                $arr1 = $this->sortReviews($reviewsWithText, $orderRating, $orderDate);
                $arr2 = $this->sortReviews($reviewsWithoutText, $orderRating, $orderDate);

                $reviews = array_merge($arr1, $arr2);
            } else {
                $arr1 = $this->sortReviews($reviews, $orderRating, $orderDate);
                $reviews = $arr1;
            }
        }
        
        $data['reviews'] = $reviews;
        $data['form'] = $form->createView();
            
        return $this->render('Reviews/index.html.twig', $data);
    }
}