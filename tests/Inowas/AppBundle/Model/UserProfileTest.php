<?php
namespace Tests\Inowas\AppBundle\Model;


use Inowas\AppBundle\Model\UserProfile;

class UserProfileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_creates_user_profile_from_array(): void
    {
        $firstName = 'fn';
        $lastName = 'ln';
        $institution = 'inst';
        $email = 'email';

        $arr = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'institution' => $institution,
            'email' => $email
        ];

        $userProfile = UserProfile::fromArray($arr);
        $this->assertInstanceOf(UserProfile::class, $userProfile);
        $this->assertEquals($firstName, $userProfile->firstName());
        $this->assertEquals($lastName, $userProfile->lastName());
        $this->assertEquals($institution, $userProfile->institution());
        $this->assertEquals($email, $userProfile->email());
    }
}
