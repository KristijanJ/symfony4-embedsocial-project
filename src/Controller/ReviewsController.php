<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ReviewsService;

class ReviewsController extends AbstractController
{
    public function index(Request $request, ReviewsService $reviewsService)
    {
        $data = [];
        $reviews = $reviewsService->fetchReviews();
        
        $form = $reviewsService->createReviewsFilterForm();
        $form->handleRequest($request);

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

                $arr1 = $reviewsService->sortReviews($reviewsWithText, $orderRating, $orderDate);
                $arr2 = $reviewsService->sortReviews($reviewsWithoutText, $orderRating, $orderDate);

                $reviews = array_merge($arr1, $arr2);
            } else {
                $arr1 = $reviewsService->sortReviews($reviews, $orderRating, $orderDate);
                $reviews = $arr1;
            }
        }
        
        $data['reviews'] = $reviews;
        $data['form'] = $form->createView();
            
        return $this->render('Reviews/index.html.twig', $data);
    }
}