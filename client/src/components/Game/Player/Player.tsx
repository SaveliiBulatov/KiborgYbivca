import { useKeyboardControls } from "@react-three/drei";
import { BallCollider, RapierRigidBody, RigidBody, vec3 } from "@react-three/rapier";
import { useEffect, useRef, useState } from "react";
import { Vector3 } from "three";
import HealthBar from "./HealthBar";
import { useFrame } from "@react-three/fiber";
import { TPlayer } from "../../../modules/Server/types";
import { Animator } from "../Sprites/Animator";

export type TPlayerProps = {
    onFire?(position: Vector3, team: number): void;
    onMovement(position: Vector3): void;
    updatePlayer(updated: TPlayer): void
} & TPlayer;

const Player = ({
    x,
    y,
    vx,
    vy,
    dx,
    dy,
    teamId,
    token,
    hp,
    onMovement,
    // onFire,
    // getPosVel,
    updatePlayer,
}: TPlayerProps) => {

    const ref = useRef<RapierRigidBody>(null!);

    // const [isShooting, setShooting] = useState<boolean>(false);

    const [controlKeys, getKeys] = useKeyboardControls();

    const mouseShoot = useRef<boolean>(false);
    const [state, setState] = useState<TPlayer>({
        x,
        y,
        vx,
        vy,
        dx,
        dy,
        hp,
        token,
        teamId,
    });

    const movementController = (up?: boolean, down?: boolean, left?: boolean, right?: boolean) => {

        if (ref.current) {

            const speed = 4;
            const velocity = new Vector3();

            if (left) {
                velocity.x -= 1;
            }
            if (right) {
                velocity.x += 1;
            }
            if (up) {
                velocity.y += 1;
            }
            if (down) {
                velocity.y -= 1;
            }

            velocity.setLength(speed);

            ref.current.setLinvel(velocity, true);

            if (velocity.length()) {
                setState({
                    ...state, x: vec3(ref.current?.translation()).x,
                    y: vec3(ref.current?.translation()).y,
                    vx: vec3(ref.current?.linvel()).x,
                    vy: vec3(ref.current?.linvel()).y,
                });
                updatePlayer(state);
            }
        }
    }

    useEffect(() => {
        const mouseDownHandler = (e: MouseEvent) => {
            if (e.button === 0) {
                mouseShoot.current = true;
            }
        }
        const mouseUpHandler = (e: MouseEvent) => {
            if (e.button === 0) {
                mouseShoot.current = false;
            }
        }

        document.addEventListener("mousedown", mouseDownHandler);
        document.addEventListener("mouseup", mouseUpHandler);

        return () => {
            document.removeEventListener("mousedown", mouseDownHandler);
            document.removeEventListener("mouseup", mouseUpHandler);
        }

    }, []);

    useFrame(() => {
        const { up, down, left, right, shoot } = getKeys();
        movementController(up, down, left, right);

        const playerPosition = vec3(ref.current?.translation());

        if (onMovement) {
            onMovement(playerPosition);
        }

        // if (shoot || isShooting) {
        //     if (onFire) {
        //         onFire(playerPosition, teamId);
        //     }
        // }


    });

    // useEffect(() => {
    //     const data = {
    //         type: 'player',
    //         team: teamId,
    //         hp: state.hp,
    //     }

    //     ref.current.userData = data;

    //     if (state.hp === 0) {
    //         ref.current.setEnabled(false);
    //     }

    // }, [state]);

    return (
        <group>
            <RigidBody
                ref={ref}
                scale={0.5}
                position={[x, y, 0]}
                colliders="hull"
                friction={1}
                linearDamping={10}
                angularDamping={1}
                lockRotations
            >

                <Animator
                    fps={2}
                    startFrame={0}
                    loop={true}
                    autoPlay={true}
                    textureImageURL={'./assets/test/Sprite-0001.png'}
                    textureDataURL={'./assets/test/Sprite-0001.json'}
                    alphaTest={0.01}
                />

                <BallCollider args={[0.5]} restitution={0}
                    onIntersectionEnter={(e) => {
                        // const data: any = e.other.rigidBody?.userData;
                        // if (data.type === "projectile") {
                        //     const damage = data.team === teamId ? data.damage / 2 : data.damage;
                        //     if (hp - damage < 0) {
                        //         setState({ ...state, hp: 0 });
                        //     } else {
                        //         setState({ ...state, hp: hp - damage });
                        //     }
                        // }
                    }} />
                <HealthBar value={hp} color={0xff0000} />
            </RigidBody>
        </group>
    );
}

export default Player;